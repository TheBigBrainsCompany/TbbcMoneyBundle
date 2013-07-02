TbbcMoneyBundle
===============

This bundle is used to integrate the [Money library from mathiasverraes](https://github.com/mathiasverraes/money) into
a symfony2 project.
This library is based on Fowler's [Money pattern](http://blog.verraes.net/2011/04/fowler-money-pattern-in-php/)

# Status

unstable

what is functionnal :

* integration of the money library
* configuration parser
* pairManager

In progress :

* form integration
* twig integration
* small administration interface for ratio management

# Overview

## integration of money library

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

## integration of money and currencies in a form

add a type money and a type currency in forms

## add a configuration for currencylist

```yaml
tbbc_money:
    currencies: ["USD", "EUR"]
    reference_currency: "EUR"
```

## currencies configurations and GUI

Convert an amount into another currency
```php
$pairManager = $this->get("tbbc_money.pair_manager");
$usd = $pairManager->convert($amount, 'USD');
```

Save an conversion value in a DB
```php
$pairManager = $this->get("tbbc_money.pair_manager");
$pairManager->saveRatio('USD', 1.25); // save in ratio file in CSV
$eur = Money::EUR(100);
$usd = $pairManager->convert($amount, 'USD');
$this->assertEquals(Money::USD(125), $usd);
```

## Twig integration

```twig
{{ $amount | money_format }}
{{ $amount | money_format("fr") }}
{{ $amount | money_as_float }}
{{ $amount | money_convert("USD") | money_format }}
```

# Versions

unstable

# Authors

* Philippe Le Van (twitter : plv), http://www.kitpages.fr