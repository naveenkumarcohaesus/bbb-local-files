<?php

namespace Drupal\bbb_core\Plugin\CustomElement;

use Drupal\cohesion_elements\CustomElementPluginBase;

/**
 * Additional behaviours for filters element plugin.
 *
 * @CustomElement(
 *   id = "bbb_list_filters",
 *   label = @Translation("Listing page filters")
 * )
 */
class ListingPageFilters extends CustomElementPluginBase {

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
        'library' => ['bbb_core/list_page_filters'],
      ],
    ];
  }

}
