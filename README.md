British Business Bank Project
====

## Local Development Setup with Lando
* lando start
* composer install
* lando ssh
* Add Site Studio Cred in `local.settings.php`
* blt setup -n
* drush @local.bbbwebsites uli

## Local Development Setup with IDE

- Login into Acquia Cloud Subscription
- Create New Cloud IDE
- Click on CONFIGURE IDE
- Configure GH via `gh auth login`

OR

- Copy the contents of your IDE SSH public key
	cat /home/ide/.ssh/id_rsa_acquia_ide_<UUID>.pub
-  Paste the SSH public key into the Key field at https://github.com/settings/ssh/new

- Clone your forked repository.
   git clone [your-URL.git] /home/ide/project
   git remote add upstream git@github.com:BritishBusinessBank/BBB-BBBwebsites.git


- Check PHP version, if 8.1 update to 8.0
    - acli ide:php-version 8.0
- Update local.settings.php
```
  $db_name = 'drupal';

/**
 * Database configuration.
 */
$databases['default']['default'] = [
  'database' => $db_name,
  'username' => 'drupal',
  'password' => 'drupal',
  'host' => 'localhost',
  'port' => '3306',
  'driver' => 'mysql',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'prefix' => '',
];
```
- `composer install`
Configure Site Studio Credentials in your `local.settings.php`
```

/**
 * Set Site Studio API keys.
 */
$config['cohesion.settings']['api_key'] = '***';
$config['cohesion.settings']['organization_key'] = '***';

```

- `blt setup`


Working on Dev DB
* Copy `example.local.blt.yml` and Rename to `local.blt.yml`
* By default BBI site alias is added in `local.blt.yml` [ remote: 'bbi.01dev']
* This can be changed to `bpc` or `bbb` or `sul`
* Run `blt sync -n` to automatically download the DB, Import SS Packages and download the files into local.


## Troubleshooting
* `COMPOSER_MEMORY_LIMIT=2Gb composer update`
Or to have unlimited memory:
* `COMPOSER_MEMORY_LIMIT=-1 composer update`

To check whether it says it's running or not
* `supervisorctl status mysqld`
Restart MYSQL
* `acli ide:service-stop mysql` - if it is not running
* `acli ide:service-start mysql` - if it is running

How to increase php memory limit?
Add below code in local.settings.php
```
if (PHP_SAPI === 'cli') {
  ini_set('memory_limit', '1048M');
}
```

This is a BBB for project providing a great out-of-the-box experience for new Drupal 9 projects hosted on Acquia. It is based on the [Drupal Recommended Project](https://github.com/drupal/recommended-project/tree/9.0.x) and similar to the [Acquia Drupal Minimal Project](https://github.com/acquia/drupal-minimal-project), with the principal difference being the addition of several modules and packages that provide the best possible out-of-the-box experience for Acquia customers.

This project includes the following packages and configuration:
* [Drupal core](https://www.drupal.org/project/drupal)
* [Drupal core scaffold](https://www.drupal.org/docs/develop/using-composer/using-drupals-composer-scaffold)
* [Acquia CMS](https://github.com/acquia/acquia-cms-starterkit) (Starter kit)
* [Drush](https://github.com/drush-ops/drush) (Drupal CLI and development tool)
* [Asset Packagist](https://asset-packagist.org/) repository, package, and configuration
* [Acquia environment detection](https://github.com/acquia/drupal-environment-detector)
* [Acquia platform memcache settings](https://github.com/acquia/memcache-settings)
* Best practices for Drupal development, testing and project architcture

The Acquia CMS starter kit allows you to install Drupal for a given style of CMS:

| Name  | Description |
| ------------- | ------------- |
| Acquia CMS Enterprise Low-code  | The low-code starter kit will install Acquia CMS with Site Studio and a UIkit. It provides drag and drop content authoring and low-code site building. An optional content model can be added in the installation process. |

## Onboarding and Local Development workflow
Please check the official BLT documentation
Instead of DrupalVM, use lando which is preconfigured in this project.
https://docs.acquia.com/blt/developer/onboarding/



You should only commit changes to `composer.json` and `composer.lock`. Do not commit files in the `vendor`, `docroot/core`, and similar directories (these are ignored by the provided `.gitignore` file). In order to run your application in another environment, you’ll need to run `composer install` to reinstall these assets

# License

Copyright (C) 2023 Acquia, Inc.

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License version 2 as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
