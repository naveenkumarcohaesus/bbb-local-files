<?php

namespace Drupal\bbb_on_this_page\Plugin\Filter;

use Drupal\bbb_on_this_page\HeadingTransform;
use Drupal\Component\Utility\Html;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Apply identifiers (anchors) to headings in content.
 *
 * @Filter(
 *   id = "heading_id_filter",
 *   title = @Translation("Automatically apply identifiers (anchors) to headings in content"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
 *   weight = 10
 * )
 */
class HeadingIdFilter extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * Word separator.
   */
  const SEPARATOR = '-';

  /**
   * Heading transform service.
   *
   * @var \Drupal\bbb_on_this_page\HeadingTransform
   */
  protected $headingTransform;

  /**
   * Constructs a \Drupal\editor\Plugin\Filter\EditorFileReference object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\bbb_on_this_page\HeadingTransform $heading_transformation
   *   The heading transform service instance.
   *
   * @internal param \Drupal\Core\Entity\EntityManagerInterface $entity_manager An entity manager object.*   An entity manager object.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, HeadingTransform $heading_transformation) {
    $this->headingTransform = $heading_transformation;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('bbb_on_this_page.heading_transform')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    return new FilterProcessResult($this->filterAttributes($text));
  }

  /**
   * Applies ids to headings.
   *
   * @param string $text
   *   The HTML text string to be filtered.
   *
   * @return string
   *   Filtered HTML.
   */
  public function filterAttributes($text) {
    $output = $text;
    $html_dom = Html::load($text);
    $xpath = new \DOMXPath($html_dom);
    $heading_tags = '//h2|//h3|//h4|//h5|//h6';

    // Apply attribute restrictions to headings.
    $headings_found = FALSE;
    foreach ($xpath->query($heading_tags) as $heading_tag) {
      $id = $this->headingTransform->transformHeadingToId($heading_tag->nodeValue);
      $heading_tag->setAttribute('id', $id);
      $headings_found = TRUE;
    }

    if ($headings_found) {
      // Only bother serializing if something changed.
      $output = Html::serialize($html_dom);
      $output = trim($output);
    }

    return $output;
  }

}
