<?php

namespace Drupal\bbb_roles_permissions\Access\FieldAccess;

/**
 * Defines an class for field access override.
 */
abstract class AbstractFieldAccessOverride implements FieldAccessOverrideInterface {

  /**
   * Indicates context of field access.
   *
   * @var array
   */
  protected static $context;

  /**
   * {@inheritdoc}
   */
  protected static function hasNodeEditPermission() {
    $entityType = self::$context['field_definition']->getTargetEntityTypeId();

    return $entityType == 'node'
      && self::$context['operation'] == 'edit'
      && !self::$context['account']->hasPermission('administer nodes');
  }

  /**
   * {@inheritdoc}
   */
  protected static function isFieldName($fieldName) {
    return self::$context['field_definition']->getName() == $fieldName;
  }

}
