<?php

namespace Drupal\bbb_partner\Plugin\CustomElement;

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
class Partner extends CustomElementPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFields() {
    return [
      'hide-alphabet' => [
        'title' => 'News Type',
        'type' => 'textfield',
        'required' => TRUE,
        'validationMessage' => 'This field is required.',
      ],
      'hide-filter' => [
        'title' => 'Number items per page',
        'type' => 'textfield',
        'required' => TRUE,
        'validationMessage' => 'This field is required.',
      ],
    ];
  }

  public function render($element_settings, $element_markup, $element_class, $element_context = []) {
    $view_name = 'partners'; // change as needed
    $display = 'partner_list'; // change as needed
 
    $view = Views::getView($view_name);
    $view->setDisplay($display);
     // Get the tid that was passed from the entity browser and pass it as a views argument.
     $uuid = $element_settings['hide-alphabet']['entity']['#entityId'];
     $entity = \Drupal::service('entity.repository')->loadEntityByUuid('taxonomy_term', $uuid);
     $id = $entity->id();
     $view->setArguments([$id]);
    // Get the number of pages that was passed from the field and pass it as a views argument.
    $view->setItemsPerPage($element_settings['hide-filter']);
    $view->preExecute();
 
    // Render the element.
    return [     
       // update "filterable_block" to your module machine name
      '#theme' => 'bbb_partner_template',
      '#elementSettings' => $element_settings,
      '#elementMarkup' => $element_markup,
      '#elementClass' => $element_class,
      '#filteredData' => $view->buildRenderable(),
    ];
  }

}
