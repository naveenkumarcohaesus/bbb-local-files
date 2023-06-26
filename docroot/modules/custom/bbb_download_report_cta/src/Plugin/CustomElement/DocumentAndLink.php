<?php

namespace Drupal\bbb_download_report_cta\Plugin\CustomElement;

use Drupal\cohesion_elements\CustomElementPluginBase;

/**
 * Generic HTML element plugin for Acquia Cohesion.
 *
 * @CustomElement(
 *   id = "document_and_link",
 *   label = @Translation("Document and link")
 * )
 */
class DocumentAndLink extends CustomElementPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFields() {
    return [
      'documentReference' => [
        'title' => 'Document Referenced',
        'type' => 'textfield',
        'required' => TRUE,
        'validationMessage' => 'This field is required.',
      ],
      'linkTitle' => [
        'title' => 'Link text',
        'type' => 'textfield',
        'required' => TRUE,
        'validationMessage' => 'This field is required.',
      ],
      'linkText' => [
        'title' => 'Link Accessible text',
        'type' => 'textfield',
        'required' => TRUE,
        'validationMessage' => 'This field is required.',
      ],
      'linkURL' => [
        'title' => 'Link URL',
        'type' => 'textfield',
        'required' => TRUE,
        'validationMessage' => 'This field is required.',
      ],
      'linkTarget' => [
        'htmlClass' => 'col-xs-12',
        'type' => 'select',
        'title' => 'Link Target',
        'nullOption' => FALSE,
        'options' => [
          '_blank' => 'New window',
          '_self' => 'Same window',
        ],
        'defaultValue' => '_self',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render($settings, $markup, $class) {
    // If document is not added, display link.
    if (!isset($settings['documentReference']['entity']['#entityId'])) {
      $link = [
        'title' => $settings['linkTitle'],
        'text' => $settings['linkText'],
        'url' => $settings['linkURL'],
        'target' => $settings['linkTarget'],
      ];
    }
    else {
      return;
    }

    // Return the rendered output.
    return [
      '#theme' => 'download_report_cta',
      '#settings' => $settings,
      '#markup' => $markup,
      '#class' => $class,
      '#link' => $link,
    ];
  }

}
