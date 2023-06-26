<?php

namespace Drupal\bbb_sitestudio_core\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\link\Plugin\Field\FieldFormatter\LinkFormatter;

/**
 * Plugin implementation of the 'link_accessibility' formatter.
 *
 * @FieldFormatter(
 *   id = "link_accessibility",
 *   label = @Translation("Link with accessbility support"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class LinkAccessibilityFormatter extends LinkFormatter {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'target' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $links = [];
    foreach ($items as $delta => $item) {
      $class = '';
      $accessibility_text = '';
      $rel = '';
      $target = '_self';
      $name = '';
      $url = $this->buildUrl($item);
      $options = $url->getOptions();
      // Set link default to url.
      $link_title = $url->toString();
      if (!empty($item->title)) {
        // Use link title if exists.
        $link_title = $item->title;
      }
      // Set value for rel attribute.
      if (isset($options['attributes']['name'])) {
        $name = $options['attributes']['name'];
        $accessibility_text = sprintf('%s', $name);
      }

      // Check if link is external.
      if (isset($options['attributes']['target'])) {
        if ($options['attributes']['target'] == '_blank') {
          $accessibility_text = sprintf('%s (%s)', $name, $this->t('opens in new window'));
          if (isset($options['external'])) {
            $accessibility_text = sprintf('%s (%s)', $name, $this->t('opens in new window - external website'));
            $class = 'external';
          }
          $target = $options['attributes']['target'];
        }
      }
      // Set value for rel attribute.
      if (isset($options['attributes']['rel'])) {
        $rel = $options['attributes']['rel'];
      }

      $links[$delta] = [
        'text' => $link_title,
        'link' => $url->toString(),
        'class' => $class,
        'accessibility_text' => $accessibility_text,
        'target' => $target,
        'rel' => $rel,
      ];
    }

    return [
      '#theme' => 'link_accessibility',
      '#links' => $links,
    ];
  }

}
