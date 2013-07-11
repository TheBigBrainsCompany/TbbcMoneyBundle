TbbcMoneyBundle
===============

[![Build Status](https://travis-ci.org/TheBigBrainsCompany/TbbcMoneyBundle.png?branch=master)](https://travis-ci.org/TheBigBrainsCompany/TbbcMoneyBundle)

This bundle is used to integrate the [Money library from mathiasverraes](https://github.com/mathiasverraes/money) into
a symfony2 project.

This library is based on Fowler's [Money pattern](http://blog.verraes.net/2011/04/fowler-money-pattern-in-php/)

This bundle is stable and tested.

Quick Start
-----------

```php
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

Table of contents
-----------------

* [Installation](#installation)
* [Usage](#usage)
* [Contributing](#contributing)
* [Requirements](#requirements)
* [Authors](#authors)
* [Status](#status)

Installation
------------

Using [Composer](http://getcomposer.org/), just `$ composer require {PACKAGIST_PACKAGE_PATH}` package or:

``` javascript
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

### Integration of money and currencies in a form

See [Form And Doctrine Integration](https://github.com/TheBigBrainsCompany/TbbcMoneyBundle/blob/master/Resources/doc/20-FormAndDoctrineIntegration.md)

### Conversion manager

Convert an amount into another currency

```php
$pairManager = $this->get("tbbc_money.pair_manager");
$usd = $pairManager->convert($amount, 'USD');
```

Save a conversion value in a DB

```php
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
```

### commands

```bash
# save a ratio in the storage
./app/console tbbc:money:save-ratio USD 1.25

# display ratio list
./app/console tbbc:money:ratio-list
```


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


Status
------

Stable

what is functionnal :

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

1.1.0 : 2013/07/04

* refactor : storage extracted in another service (CsvStorage)
* new : command creation : tbbc:money:save-ratio, tbbc:money:ratio-list

1.0.0 : 2013/07/03

* first realease


