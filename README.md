# Magento 2 Product Save Mutex
## Description
Module that ensures mutual exclusion on product saves. The locking is applied on product skus thus 2 or more
simultaneous saves on a product will result in one of the simultaneous save calls successfully saving the product
and the rest failing with a `RuntimeException`.

## Prerequisites
* PHP 5.6 or newer
* Composer  (https://getcomposer.org/download/).
* `magento/framework` 100 or newer
* `magento/module-catalog` 100, 101 or newer
* `snowio/magento2-lock` 1.0.0 or newer


## Installation
```
composer require snowio/magento2-product-save-mutex
php bin/magento module:enable SnowIO_ProductSaveMutex
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

## License
This software is licensed under the MIT License. [View the license](LICENSE)
