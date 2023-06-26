<?php

namespace Drupal\bbb_partner\Commands;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\taxonomy\Entity\Term;
use Drush\Commands\DrushCommands;

/**
 * A Drush commandfile.
 */
class FieldMigrationCommand extends DrushCommands {
  /**
   * Entity type service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;
  /**
   * Logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $loggerChannelFactory;
  /**
   * Entity Field Manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  private $entityFieldManager;

  /**
   * Constructs a new FieldMigrationCommand object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   *   Logger service.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entityFieldManager
   *   Entity Field Manager service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, LoggerChannelFactoryInterface $loggerChannelFactory, EntityFieldManagerInterface $entityFieldManager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->loggerChannelFactory = $loggerChannelFactory;
    $this->entityFieldManager = $entityFieldManager;
  }

  /**
   * Field migration command from old to new field.
   *
   * @param string $source_field
   *   Old partner field.
   * @param string $destination_field
   *   New partner field.
   *
   * @command bbb_partner:migrate
   * @aliases migrate-field
   * @options msg Whether or not an extra message should be displayed to the user.
   * @usage bbb_partner:migrate field_investment_manager field_inv_manager
   */
  public function migrate($source_field, $destination_field) {
    // Error handling.
    if (empty($source_field) || empty($destination_field)) {
      $this->loggerChannelFactory->get('bbb_partner')->error('Missing arguments in the drush commands.');
      return;
    }
    // Get all partner terms.
    $partners = $this->entityTypeManager->getStorage('taxonomy_term')
      ->loadByProperties(['vid' => 'partner']);
    // Check if field exists for one partner entity.
    $first_partner_entity = reset($partners);
    if (!$first_partner_entity->hasField($source_field) || !$first_partner_entity->hasField($destination_field)) {
      $this->loggerChannelFactory->get('bbb_partner')->error('Invalid field name given.');
      return;
    }

    foreach ($partners as $partner) {
      // Get target vocabulary.
      $bundle_fields = $this->entityFieldManager->getFieldDefinitions('taxonomy_term', 'partner');
      $field_definition = $bundle_fields[$destination_field];
      $target_vid = reset($field_definition->getSetting('handler_settings')['target_bundles']);
      // Get old field values.
      $old_values = $partner->get($source_field)->getValue();
      // Create new term id array.
      $new_term_ids = [];
      foreach ($old_values as $old_value) {
        $term = $this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties([
          'name' => $old_value,
          'vid' => $target_vid,
        ]);
        $term = reset($term);
        if ($term) {
          $new_term_ids[] = $term->id();
        }
        else {
          $term = Term::create(['name' => $old_value, 'vid' => $target_vid]);
          $term->save();
          $new_term_ids[] = $term->id();
        }
      }
      // Update values for the new field.
      foreach ($new_term_ids as $index => $tid) {
        if ($index == 0) {
          $partner->set($destination_field, $tid);
        }
        else {
          $partner->get($destination_field)->appendItem([
            'target_id' => $tid,
          ]);
        }
      }
      // Save partner taxonomy.
      $partner->save();
    }
  }

}
