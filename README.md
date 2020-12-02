# Magento 2 Product Save Mutex
## Description
Module that ensures mutual exclusion on product saves. The locking is applied on product skus thus 2 or more
simultaneous saves on a product will result in one of the simultaneous save calls successfully saving the product
and the rest failing with a `RuntimeException`.

## Magento Versions
- `<= 2.3.x` use 2.x tags
- `>= 2.4.x` use master

## Prerequisites
* PHP 7.4 or newer
* Composer  (https://getcomposer.org/download/).
* `magento/framework` 103 or newer
* `magento/module-catalog` 104 or newer
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
