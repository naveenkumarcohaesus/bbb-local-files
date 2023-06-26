<?php

namespace Drupal\bbb_roles_permissions\Access\FieldAccess;

use Drupal\Core\Access\AccessResult;

/**
 * Grant access to the "created" option.
 */
class GrantCreatedAccess extends AbstractFieldAccessOverride implements FieldAccessOverrideInterface {

  /**
   * {@inheritdoc}
   */
  public static function access(array &$grants, array $context) {
    self::$context = $context;

    if (self::hasNodeEditPermission() && self::isFieldName('created')) {
      $bundle = $context['items']->getEntity()->bundle();
      $grants[':default'] = AccessResult::allowedIfHasPermissions(
        $context['account'],
        [
          "override $bundle authored on option",
          'override all authored on option',
        ],
        'OR'
      );
    }
  }

}
