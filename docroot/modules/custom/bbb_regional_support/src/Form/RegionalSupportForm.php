<?php

namespace Drupal\bbb_regional_support\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Form for getting the entered postcode to show the nearby partners.
 *
 * @internal
 */
class RegionalSupportForm extends FormBase {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'regional_support_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $data = NULL) {

    $form['postcode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Postcode'),
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Search on postcode'),
      '#attributes' => [
        'class' => ['coh-style-label-large'],
      ],
    ];
    $form['#disable_inline_form_errors'] = TRUE;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $postcode = $form_state->getValue('postcode');
    $postcode = strtoupper(str_replace(' ', '', $postcode));
    // Validate if postcode is entered.
    if ($postcode) {
      $valid_postcode = $this->validateUkPostcode($postcode);
      if (!$valid_postcode) {
        $form_state->setErrorByName('postcode', $this->t('Please enter a valid UK postcode.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $postcode = $form_state->getValue('postcode');
    if ($postcode) {
      $url = '/regional-support-map?postcode=' . $postcode;
    }
    else {
      $url = '/regional-support-map';
    }
    $response = new RedirectResponse($url);
    $response->send();
  }

  /**
   * Validate entered postcode for UK formats.
   */
  public function validateUkPostcode($postcode) {
    $postcode = strtoupper(str_replace(' ', '', $postcode));
    if (preg_match("/(^[A-Z]{1,2}[0-9R][0-9A-Z]?[\s]?[0-9][ABD-HJLNP-UW-Z]{2}$)/i", $postcode) || preg_match("/(^[A-Z]{1,2}[0-9R][0-9A-Z]$)/i", $postcode)) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

}
