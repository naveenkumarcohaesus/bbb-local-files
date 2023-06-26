<?php

/**
 * @file
 * Used to configure the most appropriate core depending on the environment.
 */

$env = getenv('AH_SITE_ENVIRONMENT');

if ($env !== 'ci' && $env !== 'ide') {
  switch ($env) {
    case '01live':
      $settings['acquia_search']['override_search_core'] = 'BDRZ-206172.01live.bbbwebsites';
      // Set respective hash ids for search indexes.
      $config['search_api.index.solr_index_bbi']['datasource_settings']['solr_multisite_document']['target_hash'] = 'swkkxe';
      $config['search_api.index.solr_index_bpc']['datasource_settings']['solr_multisite_document']['target_hash'] = '5emys0';
      $config['search_api.index.solr_index_bbb']['datasource_settings']['solr_multisite_document']['target_hash'] = 'zl85h3';
      $config['search_api.index.solr_index_sul']['datasource_settings']['solr_multisite_document']['target_hash'] = '1iqua5';
      break;

    case '01test':
      $settings['acquia_search']['override_search_core'] = 'BDRZ-206172.01test.bbbwebsites';
      // Set respective hash ids for search indexes.
      $config['search_api.index.solr_index_bbi']['datasource_settings']['solr_multisite_document']['target_hash'] = 'zs078g';
      $config['search_api.index.solr_index_bpc']['datasource_settings']['solr_multisite_document']['target_hash'] = '332inb';
      $config['search_api.index.solr_index_bbb']['datasource_settings']['solr_multisite_document']['target_hash'] = '5kd3m0';
      // To do : Add hash id override for solr_index_sul solr index.
      break;

    case '01dev':
      $settings['acquia_search']['override_search_core'] = 'BDRZ-206172.01dev.bbbwebsites';
      break;
  }

  // Disable the search server's created by acquia_search and search_api module.
  $config['search_api.index.acquia_search_index']['status'] = FALSE;
}
