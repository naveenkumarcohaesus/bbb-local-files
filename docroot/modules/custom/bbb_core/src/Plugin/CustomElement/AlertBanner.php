<?php

namespace Drupal\bbb_core\Plugin\CustomElement;

use Drupal\cohesion_elements\CustomElementPluginBase;

/**
 * Additional behaviours for filters element plugin.
 *
 * @CustomElement(
 *   id = "bbb_alert_banner",
 *   label = @Translation("Alert Banner")
 * )
 */
class AlertBanner extends CustomElementPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFields() {
    return [
      'hidden' => [
        'type' => 'hidden',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render($element_settings, $element_markup, $element_class) {
    return [
      '#attached' => [
        'library' => ['bbb_core/alert_banner'],
      ],
    ];
  }

}
