<?php

namespace Drupal\bbb_on_this_page\Plugin\CustomElement;

use Drupal\cohesion_elements\CustomElementPluginBase;
use Drupal\cohesion_elements\Entity\CohesionLayout;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;

/**
 * Generic HTML element plugin for DX8.
 *
 * @package Drupal\cohesion\Plugin\CustomElement
 *
 * @CustomElement(
 *   id = "on_this_page",
 *   label = @Translation("Provides list of links on the page.")
 * )
 */
class OnThisPage extends CustomElementPluginBase {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getFields() {
    return [
      // Select list to decide heading type for 'On This Page' text.
      'headingTag' => [
        'htmlClass' => 'col-xs-12',
        'type' => 'select',
        'title' => 'HTML heading tag',
        'nullOption' => FALSE,
        'options' => [
          'h2' => 'h2',
          'h3' => 'h3',
          'h4' => 'h4',
          'h5' => 'h5',
          'h6' => 'h6',
        ],
      ],
      'selectHeadingLink' => [
        'htmlClass' => 'col-xs-12',
        'type' => 'select',
        'title' => 'Select Heading type for Links',
        'nullOption' => FALSE,
        'options' => [
          'h2' => 'h2',
          'h3' => 'h3',
          'h4' => 'h4',
        ],
        'defaultValue' => 'h2',
      ],
      'subsectionToggle' => [
        'htmlClass' => 'col-xs-12',
        'type' => 'checkbox',
        'title' => 'Enable subsections',
        'notitle' => FALSE,
        'defaultValue' => FALSE,
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render($settings, $markup, $class) {

    $headings = [];
    $subheading = '';
    $flag = '0';
    // Load node from the route.
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      $layout_id = $node->get('field_layout_canvas')->getValue()[0]['target_id'];
      $content = CohesionLayout::load($layout_id);
      $twig = $content->getTwig();

      if (!empty($twig)) {
        $dom = new \DOMDocument();
        @$dom->loadHTML($twig);
        $xpath = new \DOMXpath($dom);
        $htags = $xpath->query('//h5 | //h4 | //h3 | //h2');

        // Cacluate tag for the subheading,
        // for e.g if heading is 'h2' the subheading would be 'h3'.
        $subheading = "h" . ((int) explode("h", $settings['selectHeadingLink'])[1] + 1);
        $hlength = count($htags);
        /**@var \DOMElement $htag*/
        for ($i = 0; $i < count($htags); $i++) {
          $htag = $htags[$i];
          if ($settings['selectHeadingLink'] == $htag->nodeName) {
            // Set flag to know if selected heading is available on the page.
            $flag = '1';
          }
          $name = $htag->nodeName;
          if ($name == $settings['selectHeadingLink']) {
            $heading = [
              'value' => utf8_decode($this->clearNbsp($htag->nodeValue)),
              'name' => $name,
              'id' => \Drupal::service('bbb_on_this_page.heading_transform')->transformHeadingToId(utf8_decode($htag->nodeValue)),
              'children' => [],
            ];
            // If current element is a heading and subsection toggle is enabled,
            // loop through the array and check if it has any subheadings.
            if ($settings['subsectionToggle']) {
              for ($j = $i + 1; $j < $hlength; $j++) {
                $htag = $htags[$j];
                $name = $htag->nodeName;
                if ($name == $subheading) {
                  $heading['children'][] = [
                    'value' => utf8_decode($this->clearNbsp($htag->nodeValue)),
                    'name' => $name,
                    'id' => \Drupal::service('bbb_on_this_page.heading_transform')->transformHeadingToId(utf8_decode($htag->nodeValue)),
                  ];

                }
                elseif ($name == $settings['selectHeadingLink']) {
                  break;
                }
                // If current element is not a heading,
                // move the outer loop counter too.
                $i++;
              }
            }
            $headings[] = $heading;
          }
        }
      }
    }

    $headings['subheading'] = $subheading;

    return [
      '#theme' => 'on_this_page',
      '#template' => 'on-this-page',
      '#settings' => $settings,
      '#markup' => $markup,
      '#class' => $class,
      '#headings' => $headings,
      '#flag' => $flag,
    ];
  }

  /**
   * Clear nbsp from the string.
   */
  public function clearNbsp($str) {
    $entities = str_replace('&nbsp;', ' ', htmlentities($str));
    return html_entity_decode($entities);
  }

}
