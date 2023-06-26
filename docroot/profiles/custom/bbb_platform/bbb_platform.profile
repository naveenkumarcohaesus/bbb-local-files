<?php

/**
 * @file
 * Enables modules and site configuration for a bbb_profile site installation.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Return current site name.
 *
 * @return string
 *   The current site name.
 */
function _bbb_platform_get_current_site_name(): string {
  // Get site name based on enabled module.
  $moduleHandler = \Drupal::service('module_handler');
  if ($moduleHandler->moduleExists('bbi_core')) {
    return 'BBI';
  }
  if ($moduleHandler->moduleExists('bpc_core')) {
    return 'BPC';
  }
  if ($moduleHandler->moduleExists('bbb')) {
    return 'BBB';
  }
  if ($moduleHandler->moduleExists('sul_core')) {
    return 'SUL';
  }
}

/**
 * Implements hook_element_info_alter().
 *
 * Customize the Core message for BBB.
 */
function bbb_platform_element_info_alter(array &$types) {
  if (isset($types['password_confirm'])) {
    $types['password_confirm']['#process'][] = 'bbb_platform_form_process_password_confirm';
  }
}

/**
 * Form element process handler for client-side password validation.
 *
 * This #process handler is automatically invoked for 'password_confirm' form
 * elements to add the JavaScript and string translations for dynamic password
 * validation.
 */
function bbb_platform_form_process_password_confirm($element) {
  $password_settings = [
    'confirmTitle' => t('Passwords match:'),
    'confirmSuccess' => t('yes'),
    'confirmFailure' => t('no'),
    'showStrengthIndicator' => FALSE,
  ];

  if (\Drupal::config('user.settings')->get('password_strength')) {
    $password_settings['showStrengthIndicator'] = TRUE;
    $password_settings += [
      'strengthTitle' => t('Password strength:'),
      'hasWeaknesses' => t('Recommendations to make your password stronger:'),
      'tooShort' => t('Make it at least 20 characters'),
      'addLowerCase' => t('Add lowercase letters'),
      'addUpperCase' => t('Add uppercase letters'),
      'addNumbers' => t('Add numbers'),
      'addPunctuation' => t('Add punctuation'),
      'sameAsUsername' => t('Make it different from your username'),
      'weak' => t('Weak'),
      'fair' => t('Fair'),
      'good' => t('Good'),
      'strong' => t('Strong'),
      'username' => \Drupal::currentUser()->getAccountName(),
    ];
  }

  $element['#attached']['library'][] = 'user/drupal.user';
  $element['#attached']['drupalSettings']['password'] = $password_settings;

  return $element;
}

/**
 * Implements hook_form_FORM_ID_alter() for user_form().
 *
 * Change the weight of password policy field.
 */
function bbb_platform_form_user_form_alter(&$form, FormStateInterface $form_state) {
  $form['account']['pass']['#weight'] = 1;
  $form['account']['password_policy_status']['#weight'] = 2;
  $form['account']['status']['#weight'] = 3;
  $form['account']['roles']['#weight'] = 4;
}
