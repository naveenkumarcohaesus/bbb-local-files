<?php

namespace Drupal\bbb_core\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class OnetrustCookieSettings.
 *
 * Config form for Onetrust Cookie Settings.
 */
class OnetrustCookieSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bbb_core_onetrust_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'bbb_core.onetrust.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);
    $config = $this->config('bbb_core.onetrust.settings');

    $form['data_domain_script'] = [
      '#type' => 'textfield',
      '#title' => 'Data Domain Script',
      '#default_value' => $config->get('data_domain_script'),
      '#description' => $this->t('Onetrust data-domain-script value'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('bbb_core.onetrust.settings')
      ->set('data_domain_script', $form_state->getValue('data_domain_script'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
