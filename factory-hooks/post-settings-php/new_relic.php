<?php

/**
 * @file
 * ACSF New Relic Configuration.
 * @var string $site_path
 * This is always set and exposed by the Drupal Kernel.
 */

use Acquia\Blt\Robo\Common\EnvironmentDetector;

if (extension_loaded('newrelic')) {
  $env = 'local';

  if (EnvironmentDetector::isAcsfEnv()) {
    $env = EnvironmentDetector::getAhEnv();
    $site_name = EnvironmentDetector::getSiteName($site_path);
  }

  newrelic_set_appname("bbbwebsites.$env.$site_name; bbbwebsites.$env", '', 'true');
}
