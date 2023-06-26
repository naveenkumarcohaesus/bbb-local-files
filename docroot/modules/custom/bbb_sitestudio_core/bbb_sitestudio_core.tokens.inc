<?php

/**
 * @file
 * Tokens for entity fields with formatter.
 */

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\field\Entity\FieldConfig;
use Drupal\views\ViewExecutable;

/**
 * Implements hook_tokens().
 */
function bbb_sitestudio_core_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  /** @var \Drupal\token\Token $token_service */
  $token_service = \Drupal::token();
  $replacements = [];

  // Entity tokens.
  if ($type == 'entity' && !empty($data['entity'])) {
    /** @var \Drupal\Core\Entity\ContentEntityBase $entity */
    $entity = $data['entity'];
    if ($entity instanceof ViewExecutable) {
      return;
    }
    $entity_type_id = $entity->getEntityTypeId();
    if ($entity_type_id === 'webform' || $entity_type_id === 'views') {
      return;
    }
    if ($entity_type_id == 'taxonomy_term') {
      $entity_type_id = 'term';
    }
    $fields = $entity->getFieldDefinitions();
    $token_type = 'formatted';
    /** @var \Drupal\field\Entity\FieldConfig $field */
    foreach ($fields as $field_name => $field) {

      if ($field instanceof FieldConfig) {
        // Create attribute tokens for link field.
        if ($field->getType() == 'link') {
          $values = $entity->{$field_name}->getValue();
          if (!empty($values) && isset($values[0]['options']['attributes'])) {
            foreach ($values[0]['options']['attributes'] as $key => $attr) {
              $field_tokens = $token_service->findWithPrefix($tokens, "{$field_name}-attributes");
              if (isset($field_tokens[$key])) {
                $replacements[$field_tokens[$key]] = $attr;
              }
            }
          }
        }

        $field_tokens = $token_service->findWithPrefix($tokens, "{$field_name}-{$token_type}");
        if ($field_tokens) {
          foreach ($field_tokens as $name => $original) {
            /** @var \Drupal\Core\Field\FieldItemList $items */
            $items = $entity->{$field_name};
            if (!$items->isEmpty()) {
              $render = $items->view([
                'type' => $name,
                'label' => 'hidden',
              ]);
              $replacements[$original] = \Drupal::service('renderer')->render($render);
            }
          }
        }
      }
    }
  }

  return $replacements;
}
