# Netresearch Vault Importer

Extension to provide CLI commands to import secrets from vault into Magento's config.

## Facts

* version: 0.1.0

## Description

This Magento® 2 Module enables you to import secrets from a [Vault](https://www.vaultproject.io/)
secret storage and apply them to the Magento config.

Values stored as JSON within Vault will be recursively stored in the Magento® `core.config.data`
database table.

## Requirements

* PHP 5.6.5
* PHP >= 7.0.6
* PHP >= 7.1.0
* PHP >= 7.2.0

## Compatibility

* Magento® >= 2.2.0+
* Magento® >= 2.3.0+

## Installation Instructions

Run this command to add the extension to your composer requirements:

    composer require netresearch/module-vault-import

Once the source files are available, make them known to the application:

    ./bin/magento module:enable Netresearch_VaultImport
    ./bin/magento setup:upgrade

Last but not least, flush cache and compile.

    ./bin/magento cache:flush
    ./bin/magento setup:di:compile

## Usage

Run the following command to print usage information:

    ./bin/magento vault:import --help

## Uninstallation

To unregister the module from the application, run the following command:

    ./bin/magento module:uninstall Netresearch_VaultImport
    composer update
    
This will automatically remove source files and update package dependencies.

Developer
---------
* Paul Siedler | [Netresearch GmbH & Co. KG](http://www.netresearch.de/) | [@powlomat](https://twitter.com/powlomat)
* Max Melzer | [Netresearch GmbH & Co. KG](http://www.netresearch.de/) | [@_maxmelzer](https://twitter.com/_maxmelzer)

License
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2019 Netresearch DTT GmbH
