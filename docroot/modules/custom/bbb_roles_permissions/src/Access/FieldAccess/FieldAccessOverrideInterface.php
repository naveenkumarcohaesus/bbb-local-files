<?php

namespace Drupal\bbb_roles_permissions\Access\FieldAccess;

/**
 * Defines an interface for field access override.
 */
interface FieldAccessOverrideInterface {

  /**
   * {@inheritdoc}
   */
  public static function access(array &$grants, array $context);

}
