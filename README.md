TbbcMoneyBundle
===============

[![Build Status](https://travis-ci.org/TheBigBrainsCompany/TbbcMoneyBundle.png?branch=master)](https://travis-ci.org/TheBigBrainsCompany/TbbcMoneyBundle)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/cb69e820-135b-4906-93fd-7921ba46a6e6/big.png)](https://insight.sensiolabs.com/projects/cb69e820-135b-4906-93fd-7921ba46a6e6)

This bundle is used to integrate the [Money library from mathiasverraes](https://github.com/mathiasverraes/money) into
a symfony2 project.

This library is based on Fowler's [Money pattern](http://blog.verraes.net/2011/04/fowler-money-pattern-in-php/)

This bundle is stable and tested.

Quick Start
-----------

```php
use Money\Money;

// a money library
$fiveEur = Money::EUR(500);
$tenEur = $fiveEur->add($fiveEur);
list($part1, $part2, $part3) = $tenEur->allocate(array(1, 1, 1));
assert($part1->equals(Money::EUR(334)));
assert($part2->equals(Money::EUR(333)));
assert($part3->equals(Money::EUR(333)));

// a service that stores conversion ratios
$pairManager = $this->get("tbbc_money.pair_manager");
$usd = $pairManager->convert($tenEur, 'USD');

// a form integration
$formBuilder->add("price", "tbbc_money");
```

Features
--------

* Integrates money library from mathiasverraes
* Twig filters and formater in order to display amounts
* A storage system for currency ratios
* A ratioProvider system for fetching ratio from externals api
* Symfony2 form integration
* Console commands for different operations
* A configuration parser for specifying website used currencies

Table of contents
-----------------

* [Installation](#installation)
* [Usage](#usage)
* [Storage](#storage)
* [Contributing](#contributing)
* [Requirements](#requirements)
* [Authors](#authors)
* [Status](#status)
* [Versions](#versions)

Installation
------------

Using [Composer](http://getcomposer.org/), just `$ composer require tbbc/money-bundle` package or:

```javascript
{
  "require": {
    "tbbc/money-bundle": "dev-master"
  }
}
```

Then add the bundle in AppKernel :

```php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new \Tbbc\MoneyBundle\TbbcMoneyBundle(),
        );
    }
```

in your config.php, add the currencies you want to use and the reference currency.

```yaml
tbbc_money:
    currencies: ["USD", "EUR"]
    reference_currency: "EUR"
```

In your config.yml, add the form fields presentations

```yaml
twig:
    form:
        resources:
            - 'TbbcMoneyBundle:Form:fields.html.twig'
```


Usage
-----

### Money Library integration

```php
use Money\Money;

$fiveEur = Money::EUR(500);
$tenEur = $fiveEur->add($fiveEur);
list($part1, $part2, $part3) = $tenEur->allocate(array(1, 1, 1));
assert($part1->equals(Money::EUR(334)));
assert($part2->equals(Money::EUR(333)));
assert($part3->equals(Money::EUR(333)));

$pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);
$usd = $pair->convert($tenEur);
$this->assertEquals(Money::USD(1250), $usd);
```

### Integration of money and currencies in a form and link to doctrine

You have 3 new form types :
* tbbc_currency : asks for a currency among currencies defined in config.yml
* tbbc_money : asks for an amount and a currency
* tbbc_simple_money : asks for an amount and sets the currency to the reference currency set in config.yml

You can see more details on how to manage forms and doctrine binding in this page :
[Form And Doctrine Integration](https://github.com/TheBigBrainsCompany/TbbcMoneyBundle/blob/master/Resources/doc/20-FormAndDoctrineIntegration.md)

### Conversion manager

Convert an amount into another currency

```php
$pairManager = $this->get("tbbc_money.pair_manager");
$usd = $pairManager->convert($amount, 'USD');
```

Save a conversion value in a DB

```php
use Money\Money;

$pairManager = $this->get("tbbc_money.pair_manager");
$pairManager->saveRatio('USD', 1.25); // save in ratio file in CSV
$eur = Money::EUR(100);
$usd = $pairManager->convert($amount, 'USD');
$this->assertEquals(Money::USD(125), $usd);
```

### Twig integration

```twig
{{ $amount | money_format }}
{{ $amount | money_as_float }}
{{ $amount | money_get_currency | currency_symbol }}
{{ $amount | money_get_currency | currency_name }}
{{ $amount | money_convert("USD") | money_format }}
{{ $amount | money_format_currency }}
```

### commands

```bash
# save a ratio in the storage
./app/console tbbc:money:ratio-save USD 1.25

# display ratio list
./app/console tbbc:money:ratio-list

# fetch all the ratio for all defined currencies from an external API
./app/console tbbc:money:ratio-fetch
```

RatioStorage
------------

Two storages for storing ratios are available : CSV File, or Doctrine
By default, TbbcMoneyBundle is configured with CSV File.

If you want to switch to a Doctrine storage, edit your **config.yml**

```yaml
tbbc_money:
    storage: doctrine
```

Update your database schema :
```bash
./app/console doctrine:schema:update --force
```

With the Doctrine storage, currency ratio will use the default entity manager and will store data inside the **tbbc_money_doctrine_storage_ratios**

Contributing
------------

1. Take a look at the [list of issues](https://github.com/TheBigBrainsCompany/TbbcMoneyBundle/issues).
2. Fork
3. Write a test (for either new feature or bug)
4. Make a PR

Requirements
------------

* PHP 5.3+
* Symfony 2.1 +

Authors
-------

Philippe Le Van - [kitpages.fr](http://www.kitpages.fr) - twitter : @plv  
Thomas Tourlourat - [Wozbe](http://wozbe.com) - twitter: @armetiz  


Status
------

Stable

what is functional :

* integration of the money library
* configuration parser
* pairManager
* travis-ci integration
* form integration
* twig presentation for forms
* twig filters
* commands for ratio creation and ratio display

In progress :

* small administration interface for ratio management

Versions
--------

1.4.0 : 26/07/2013

* fix : datatransformer returned a null values for amounts above 1000 with a space grouping separator
* new : tbbc_simple_money field type without currency (reference currency used by default)

1.3.0 : 16/07/2013

* new : doctrine storage (thanks to @armetiz)

1.2.0 : 12/07/2013

* new : ratio provider mecanism for fetch currency ratios from external api
* Warning : small BC Break : command save-ratio is renamed ratio-save
* doc enhancement

1.1.0 : 2013/07/04

* refactor : storage extracted in another service (CsvStorage)
* new : command creation : tbbc:money:ratio-save, tbbc:money:ratio-list

1.0.0 : 2013/07/03

* first realease


