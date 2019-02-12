Netresearch Vault Importer
==========================

Extension to provide CLI commands to import secrets from vault into Magento's config.

Facts
-----
* version: 0.1.0

Description
-----------

Enables you to import secrets from a [Vault](https://www.vaultproject.io/) secret storage and apply them (recursively) to the Magento config.

Requirements
------------

* PHP 5.6.5
* PHP >= 7.0.6
* PHP >= 7.1.0
* PHP >= 7.2.0

Compatibility
-------------
* Magento >= 2.1.0+
* Magento >= 2.2.0+
* Magento >= 2.3.0+

Installation Instructions
-------------------------
Simply run this command to add the extension to your composer requirements:
```
composer require netresearch/module-vault-import

```


### Enable Module ###
Once the source files are available, make them known to the application:

    ./bin/magento module:enable Netresearch_VaultImport
    ./bin/magento setup:upgrade

Last but not least, flush cache and compile.

    ./bin/magento cache:flush
    ./bin/magento setup:di:compile

Uninstallation
--------------

The following sections describe how to uninstall the module from your Magento® 2 instance. 

#### Composer VCS and Composer Artifact ####

To unregister the shipping module from the application, run the following command:

    ./bin/magento module:uninstall Netresearch_VaultImport
    composer update
    
This will automatically remove source files, update package dependencies.

*Please note that automatic uninstallation is only available on Magento version 2.2 or newer.
On Magento 2.1 and below, please use the following manual uninstallation method.*

#### Manual Steps ####

To uninstall the module manually, run the following commands in your project
root directory:

    ./bin/magento module:disable Netresearch_VaultImport
    composer remove dhl/module-vault-import

Developer
---------
* Paul Siedler | [Netresearch GmbH & Co. KG](http://www.netresearch.de/) | [@powlomat](https://twitter.com/powlomat)

License
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2019 Netresearch DTT GmbH
