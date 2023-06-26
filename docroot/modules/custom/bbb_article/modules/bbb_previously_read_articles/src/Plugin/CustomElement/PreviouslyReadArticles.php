<?php

namespace Drupal\bbb_previously_read_articles\Plugin\CustomElement;

use Drupal\cohesion_elements\CustomElementPluginBase;

/**
 * Generic HTML element plugin for Acquia Cohesion.
 *
 * @CustomElement(
 *   id = "previously_read_articles",
 *   label = @Translation("Previously read articles")
 * )
 */
class PreviouslyReadArticles extends CustomElementPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFields() {
    return [
      'headingTag' => [
        'htmlClass' => 'col-xs-12',
        'type' => 'select',
        'title' => 'Heading Tag',
        'nullOption' => FALSE,
        'options' => [
          'h2' => 'h2',
          'h3' => 'h3',
          'h4' => 'h4',
          'h5' => 'h5',
          'h6' => 'h6',
        ],
        'defaultValue' => 'h2',
      ],
      'headingText' => [
        'htmlClass' => 'col-xs-12',
        'title' => 'Heading Text',
        'defaultValue' => 'Your previously read articles',
        'type' => 'textfield',
        'placeholder' => 'Enter Heading Text',
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
    $settings['lang'] = \Drupal::languageManager()->getCurrentLanguage()->getId();
    return [
      '#theme' => 'previously_read_articles',
      '#template' => 'previously-read-articles',
      '#settings' => $settings,
      '#markup' => $markup,
      '#class' => $class,
    ];
  }

}
