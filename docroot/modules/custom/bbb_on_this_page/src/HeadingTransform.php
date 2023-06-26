<?php

namespace Drupal\bbb_on_this_page;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Transliteration\PhpTransliteration;

/**
 * Provides utility functions for generating element IDs.
 */
class HeadingTransform {
  /**
   * Word separator.
   */
  const SEPARATOR = '-';

  /**
   * Transliteration service.
   *
   * @var \Drupal\Core\Transliteration\PhpTransliteration
   */
  protected $transliteration;

  /**
   * Constructs a \Drupal\editor\Plugin\Filter\EditorFileReference object.
   *
   * @param \Drupal\Core\Transliteration\PhpTransliteration $transliteration
   *   The transliteration service instance.
   */
  public function __construct(PhpTransliteration $transliteration) {
    $this->transliteration = $transliteration;
  }

  /**
   * Creates a machine name based on the heading.
   *
   * Inspired by Drupal\migrate\Plugin\migrate\process\MachineName::transform.
   * Improved by Drupal\pathauto\AliasCleaner.
   *
   * @param string $heading
   *   String to convert to id.
   *
   * @return mixed
   *   A dash separated id.
   */
  public function transformHeadingToId($heading) {
    // Reduce to ascii.
    $new_value = $this->transliteration->transliterate($heading);
    // Reduce to letters and numbers.
    $new_value = preg_replace('/[^a-zA-Z0-9\/]+/', static::SEPARATOR, $new_value);
    // Remove consecutive separators.
    $new_value = preg_replace('/' . static::SEPARATOR . '+/', static::SEPARATOR, $new_value);
    // Remove leading and trailing separators.
    $new_value = trim($new_value, static::SEPARATOR);
    // Convert to lowercase.
    $new_value = mb_strtolower($new_value);
    // Truncate to 128 chars.
    return Unicode::truncate($new_value, 128, TRUE);
  }

}
