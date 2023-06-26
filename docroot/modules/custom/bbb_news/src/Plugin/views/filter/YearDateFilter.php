<?php

namespace Drupal\bbb_news\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Filters for node created field.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("year_date_filter")
 */
class YearDateFilter extends FilterPluginBase {

  /**
   * {@inheritdoc}
   */
  public function buildExposedForm(&$form, FormStateInterface $form_state) {
    $form['value'] = !empty($form['value']) ? $form['value'] : [];
    parent::buildExposedForm($form, $form_state);
    $filter_id = $this->getFilterId();

    // Year filter field.
    $form[$filter_id] = [
      '#type' => 'checkboxes',
      '#options' => $this->getYearsOptions(),
    ];

    if (!empty($this->options['expose']['multiple'])) {
      $form[$filter_id]['#multiple'] = TRUE;
    }

  }

  /**
   * This method returns the ID of the fake field which contains this plugin.
   *
   * @return string
   *   ID of the field which contains this plugin.
   */
  private function getFilterId() {
    return $this->options['expose']['identifier'];
  }

  /**
   * Generates years that will be selectable.
   *
   * @return array
   *   Array with all years.
   */
  private function getYearsOptions() {

    $start_year = date('Y') - 4;
    $year_until = date('Y');
    $years = range($start_year, $year_until);

    foreach ($years as $year) {
      $return[$year] = $year;
    }

    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();

    if (!$this->options['exposed']) {
      // Administrative value.
      $this->queryFilter($this->options['node_year']);
    }
    else {
      // Exposed value.
      if (empty($this->value)) {
        return;
      }

      $this->queryFilter($this->value);
    }
  }

  /**
   * Filters by given years.
   *
   * @param array $years
   *   Years selected.
   */
  private function queryFilter(array $years) {
    $years = array_filter($years);
    $this->query->addTable("node__field_data");
    $group_id = $this->query->setWhereGroup('OR');

    foreach ($years as $year) {
      $next_year = $year + 1;

      $first = $year . '-01-01T00:00:00';
      $last = $next_year . '-01-01T00:00:00';
      $firstTime = strtotime($first);
      $lastTime = strtotime($last);
      $this->query->addWhereExpression($group_id, "node_field_data.created >= {$firstTime} AND node_field_data.created < {$lastTime}", []);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validate() {
    if (!empty($this->value)) {
      parent::validate();
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    if (!$this->options['exposed']) {
      $form['node_year'] = [
        '#type' => 'select',
        '#title' => $this->t('Year'),
        '#options' => $this->getYearsOptions(),
        '#default_value' => $this->options['node_year'] ?? NULL,
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['node_year'] = ['default' => ''];
    $options['field_name'] = ['default' => 'created'];
    $options['year_from'] = ['default' => 2012];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {

    $form['year_from'] = [
      '#type' => 'number',
      '#title' => $this->t('Year from'),
      '#description' => $this->t('Customize the year select range'),
      '#default_value' => ($this->options['year_from']) ?: 2012,
    ];

    parent::buildOptionsForm($form, $form_state);
  }

}
