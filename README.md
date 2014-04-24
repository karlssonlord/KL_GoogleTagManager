# Magento Google Tag Manager

Minimalistic [Google Tag Manager](https://www.google.com/tagmanager/) integration for Magento with Data Layer support.


## Installation

The easiest way to install the module is by using [modman](https://github.com/karlssonlord/modman):

1. `modman clone git@github.com:karlssonlord/KL_GoogleTagManager.git`
2. `modman deploy KL_GoogleTagManager`
3. Clear the cache

### Settings

Admin > Configuration > Sales > Google API > Google Tag Manager


## Data Layer Variables

* `event` - string "transaction"
* `transactionId` - string
* `transactionAffiliation` - string
* `transactionCurrency` - string
* `transactionTotal` - numeric
* `transactionShipping` - numeric
* `transactionTax` - numeric
* `transactionProducts` - array of TransactionProduct objects
    * `name` - string
    * `sku` - string
    * `category` - string
    * `price` - numeric
    * `quantity` - numeric
* `nthPurchase` - numeric
* `customerId` - numeric
* `daysSinceLastTransaction` - numeric


## Magento Config Paths

* `google/tagmanager/active`
* `google/tagmanager/container`


## Other Google Tag Manager Modules

* [Magento_GoogleTagManager](https://github.com/CVM/Magento_GoogleTagManager)