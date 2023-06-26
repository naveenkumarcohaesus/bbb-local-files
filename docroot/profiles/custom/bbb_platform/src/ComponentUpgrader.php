<?php

namespace Drupal\bbb_platform;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Tests\Core\Entity\RevisionableEntity;

/**
 * Service description.
 */
class ComponentUpgrader {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a Component Upgrade object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Add a new component field to existing content.
   *
   * @param string $componentId
   *   The id of the component.
   * @param string $newFieldUUID
   *   The UUID of the field in the component form.
   * @param mixed $defaultValue
   *   The value you wish to instantiate the new field with.
   */
  public function addNewFieldToInstances(string $componentId, string $newFieldUUID, mixed $defaultValue): void {
    $this->updateLayoutCanvasElement($componentId, $newFieldUUID, $defaultValue);
  }

  /**
   * For all content entities, initialise a field in the canvas.
   *
   * @param string $componentId
   *   The id of the component.
   * @param string $newFieldUUID
   *   The UUID of the field in the component form.
   * @param mixed $defaultValue
   *   The value you wish to instantiate the new field with.
   */
  private function updateLayoutCanvasElement(string $componentId, string $newFieldUUID, mixed $defaultValue): void {

    try {
      $idQuery = $this->entityTypeManager->getStorage('cohesion_layout')->getQuery();
      $entityData = $this->entityTypeManager->getStorage('cohesion_layout');

      foreach (array_chunk($idQuery->execute(), 25) as $entityIds) {
        if ($entityData && ($entities = $entityData->loadMultiple($entityIds))) {
          /** @var \Drupal\cohesion_elements\Entity\CohesionLayout $entity */
          foreach ($entities as $entity) {
            // Re-save the entity.
            if ($entity instanceof RevisionableEntity && $entity->getRevisionId()) {
              $entity->setNewRevision(FALSE);
            }

            // Check and Update the entity. If changed then update translations.
            if ($this->updateEntityComponent($entity, $componentId, $newFieldUUID, $defaultValue)) {

              // Update translations.
              $languages = $entity->getTranslationLanguages(FALSE);
              foreach ($languages as $lang_code => $language) {
                $translated_entity = $entity->getTranslation($lang_code);
                // Re-save the entity.
                if ($translated_entity instanceof RevisionableEntity && $translated_entity->getRevisionId()) {
                  $translated_entity->setNewRevision(FALSE);
                }
                $this->updateEntityComponent($translated_entity, $componentId, $newFieldUUID, $defaultValue);
              }
            }
          }
        }
      }
    }
    catch (\Exception $e) {

    }
  }

  /**
   * Search for component and initialise the value.
   *
   * @param mixed $entity
   *   The entity we are updating.
   * @param string $componentId
   *   The id of the component.
   * @param string $newFieldUUID
   *   The UUID of the field in the component form.
   * @param mixed $defaultValue
   *   The value you wish to instantiate the new field with.
   *
   * @return bool
   *   Returns TRUE if the entity had changes made, false otherwise.
   */
  private function updateEntityComponent(mixed $entity, string $componentId, string $newFieldUUID, mixed $defaultValue): bool {

    $json_values = $entity->getDecodedJsonValues(TRUE);
    if (property_exists($json_values, 'canvas') && property_exists($json_values, 'model')) {
      $updateUUID = $this->walkCanvasForComponent($json_values->canvas, $componentId);

      // Only save if the entity is being changed.
      if (count($updateUUID) > 0) {
        foreach ($updateUUID as $update) {
          if (!isset($json_values->model->$update->$newFieldUUID)) {
            $json_values->model->$update->$newFieldUUID = $defaultValue;
          }
        }

        $entity->setJsonValue(json_encode($json_values));
        $entity->save();

        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Search the canvas for the component that needs updating.
   *
   * @param mixed $elements
   *   The list of elements in the canvas.
   * @param string $componentId
   *   The internal ID of the component.
   *
   * @return array
   *   An array of UUIDs that correspond to the component.
   */
  private function walkCanvasForComponent(mixed $elements, string $componentId): array {

    $componentsToAlter = [];

    foreach ($elements as $value) {
      if (property_exists($value, 'uuid')) {

        if (property_exists($value, 'componentId') && $value->componentId == $componentId) {
          $componentsToAlter[] = $value->uuid;
        }

        // The canvas is nested so recurse lower levels to find our component.
        if (property_exists($value, 'children') && is_array($value->children)) {
          $children = $this->walkCanvasForComponent($value->children, $componentId);

          if (count($children) > 0) {
            $componentsToAlter = array_merge($componentsToAlter, $children);
          }
        }
      }
    }
    return $componentsToAlter;
  }

}
