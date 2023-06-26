<?php

namespace Drupal\bbb_menu_content\Plugin\CustomElement;

use Drupal\cohesion_elements\CustomElementPluginBase;
use Drupal\node\NodeInterface;

/**
 * Entity Reference field from menu item.
 *
 * @package Drupal\cohesion\Plugin\CustomElement
 *
 * @CustomElement(
 *   id = "menu_reference_content",
 *   label = @Translation("Menu Reference Content")
 * )
 */
class MenuReferenceContent extends CustomElementPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFields() {
    return [
      'nid' => [
        'htmlClass' => 'col-xs-12',
        'title' => 'Menu Content Reference ID',
        'type' => 'textfield',
        'required' => TRUE,
        'validationMessage' => 'This field is required.',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render($element_settings, $element_markup, $element_class, $element_context = []) {
    if (empty($element_settings['nid'])) {
      return [];
    }
    $view_mode = 'menu_content';
    $builder = \Drupal::service('entity_type.manager')->getViewBuilder('node');
    $storage = \Drupal::service('entity_type.manager')->getStorage('node');
    $node = $storage->load($element_settings['nid']);
    if ($node instanceof NodeInterface) {
      $build = $builder->view($node, $view_mode);
      $output = \Drupal::service('renderer')->render($build);

      return $output;
    }
    else {
      return [];
    }
  }

}
