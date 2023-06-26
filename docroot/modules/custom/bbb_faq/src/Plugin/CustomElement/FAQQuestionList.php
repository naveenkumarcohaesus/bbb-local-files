<?php

namespace Drupal\bbb_faq\Plugin\CustomElement;

use Drupal\cohesion_elements\CustomElementPluginBase;
use Drupal\views\Views;

/**
 * Generic HTML element plugin for Acquia Cohesion.
 *
 * @CustomElement(
 *   id = "faq_question_list",
 *   label = @Translation("FAQ question list")
 * )
 */
class FAQQuestionList extends CustomElementPluginBase {

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
      'termReference' => [
        'title' => 'Parent Term Reference',
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
    $view_name = 'faq_question_list';
    $display = 'block_1';

    // We get the taxonomy term UUID on
    // $element_settings['termReference']['entity']['#entityId']
    // and we need to convert it to tid. We need the Term for that.
    $entityTypeManager = \Drupal::entityTypeManager();
    $term = $entityTypeManager->getStorage('taxonomy_term')->loadByProperties([
      'uuid' => $element_settings['termReference']['entity']['#entityId'],
    ]);

    if (empty($term)) {
      return;
    }
    // Convert the object to Drupal\taxonomy\Entity\Term.
    $term = reset($term);

    $view = Views::getView($view_name);
    $view->setDisplay($display);
    // Get the tid and pass it as a views argument.
    $view->setArguments([$term->id(), $element_settings['headingTag']]);

    // Render the element.
    return [
      '#theme' => 'bbb_faq_template',
      '#elementSettings' => $element_settings,
      '#elementMarkup' => $element_markup,
      '#elementClass' => $element_class,
      '#filteredData' => $view->buildRenderable(),
    ];
  }

}
