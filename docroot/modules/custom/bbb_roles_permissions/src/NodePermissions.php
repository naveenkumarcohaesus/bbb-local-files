<?php

namespace Drupal\bbb_roles_permissions;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\Entity\NodeType;

/**
 * Provides dynamic override permissions for nodes of different types.
 */
class NodePermissions {

  use StringTranslationTrait;

  /**
   * Returns an array of additional permissions.
   *
   * @return array
   *   An array of permissions.
   */
  public function nodeTypePermissions() {
    $permissions = [];

    $this->addSpecificPermissions($permissions);

    return $permissions;
  }

  /**
   * Add node type specific permissions.
   *
   * @param array $permissions
   *   The permissions array, passed by reference.
   */
  private function addSpecificPermissions(array &$permissions) {
    /** @var \Drupal\node\Entity\NodeType $node_type */
    foreach (NodeType::loadMultiple() as $node_type) {
      $type = $node_type->id();
      $label = $node_type->label();

      $permissions["override $type sticky option"] = [
        'title' => $this->t("Override %name sticky option.", ["%name" => $label]),
      ];

      $permissions["override $type authored on option"] = [
        'title' => $this->t("Override %name authored on option.", ["%name" => $label]),
      ];
    }
  }

}
