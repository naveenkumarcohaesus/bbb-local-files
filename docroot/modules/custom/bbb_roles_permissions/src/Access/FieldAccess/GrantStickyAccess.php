<?php

namespace Drupal\bbb_roles_permissions\Access\FieldAccess;

use Drupal\Core\Access\AccessResult;

/**
 * Grant access to the "sticky" option.
 */
class GrantStickyAccess extends AbstractFieldAccessOverride implements FieldAccessOverrideInterface {

  /**
   * {@inheritdoc}
   */
  public static function access(array &$grants, array $context) {
    self::$context = $context;

    if (self::hasNodeEditPermission() && self::isFieldName('sticky')) {
      $bundle = $context['items']->getEntity()->bundle();
      $grants[':default'] = AccessResult::allowedIfHasPermissions(
        $context['account'],
        [
          "override $bundle sticky option",
          'override all sticky option',
        ],
        'OR'
      );
    }
  }

}
