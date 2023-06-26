<?php

namespace Drupal\bbb_news\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Filters for node created field.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("month_date_filter")
 */
class MonthDateFilter extends FilterPluginBase {

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
      '#options' => $this->getMonthsOptions(),
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
   * Generates all the months that will be selectable.
   *
   * @return array
   *   Array with all months.
   */
  private function getMonthsOptions() {
    return [
      "1" => $this->t('January'),
      "2" => $this->t('February'),
      "3" => $this->t('March'),
      "4" => $this->t('April'),
      "5" => $this->t('May'),
      "6" => $this->t('June'),
      "7" => $this->t('July'),
      "8" => $this->t('August'),
      "9" => $this->t('September'),
      "10" => $this->t('October'),
      "11" => $this->t('November'),
      "12" => $this->t('December'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();

    if (!$this->options['exposed']) {
      // Administrative value.
      $this->queryFilter($this->options['node_month']);
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
   * Filters by given months.
   *
   * @param array $months
   *   Months selected.
   */
  private function queryFilter(array $months) {
    $months = array_filter($months);
    $this->query->addTable("node__field_data");
    $group_id = $this->query->setWhereGroup('OR');

    foreach ($months as $month) {
      $monthWithZero = $this->formatMonth($month);
      $this->query->addWhereExpression($group_id, "from_unixtime(node_field_data.created, '%m') = {$monthWithZero}", []);
    }
  }

  /**
   * Add a zero to the left if needed.
   *
   * @param int $month
   *   Month.
   *
   * @return mixed
   *   Month with two ciphers.
   */
  private function formatMonth(int $month) {
    return ($month < 10) ? '0' . $month : $month;
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
      $form['node_month'] = [
        '#type' => 'select',
        '#title' => $this->t('Month'),
        '#options' => $this->getMonthsOptions(),
        '#default_value' => $this->options['node_month'] ?? NULL,
      ];
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['node_month'] = ['default' => ''];
    $options['field_name'] = ['default' => 'created'];

    return $options;
  }

}
