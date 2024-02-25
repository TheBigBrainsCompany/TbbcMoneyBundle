TbbcMoneyBundle
===============

[![Build Status](https://github.com/TheBigBrainsCompany/TbbcMoneyBundle/actions/workflows/code_checks.yaml/badge.svg)](https://github.com/TheBigBrainsCompany/TbbcMoneyBundle/actions/workflows/code_checks.yaml)
[![PHP](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg?style=flat-square)](https://php.net)
[![Symfony](https://img.shields.io/badge/symfony-%5E5|%5E6|%5E7-green.svg?style=flat-square)](https://symfony.com)
[![Downloads](https://img.shields.io/packagist/dt/tbbc/money-bundle.svg?style=flat-square)](https://packagist.org/packages/tbbc/money-bundle/stats)
[![Latest Stable Version](https://img.shields.io/packagist/v/tbbc/money-bundle.svg)](https://github.com/TheBigBrainsCompany/TbbcMoneyBundle/releases/latest)
[![license](https://img.shields.io/github/license/TheBigBrainsCompany/TbbcMoneyBundle.svg?style=flat-square)](https://github.com/TheBigBrainsCompany/TbbcMoneyBundle/blob/master/LICENSE)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/cb69e820-135b-4906-93fd-7921ba46a6e6/big.png)](https://insight.sensiolabs.com/projects/cb69e820-135b-4906-93fd-7921ba46a6e6)

This bundle is used to integrate the [Money library](https://github.com/moneyphp/money) into
a Symfony project.

This library is based on Fowler's [Money pattern](https://verraes.net/2011/04/fowler-money-pattern-in-php/)

* This bundle is tested and is stable with Symfony 5.4, 6.4, 7.0

Quick Start
-----------

```php
use Money\Money;
use Tbbc\MoneyBundle\Form\Type\MoneyType;

// the money library
$fiveEur = Money::EUR(500);
$tenEur = $fiveEur->add($fiveEur);
[$part1, $part2, $part3] = $tenEur->allocate([1, 1, 1]);
assert($part1->equals(Money::EUR(334)));
assert($part2->equals(Money::EUR(333)));
assert($part3->equals(Money::EUR(333)));

// a service that stores conversion ratios
$pairManager = $this->get('tbbc_money.pair_manager');
$usd = $pairManager->convert($tenEur, 'USD');

// a form integration
$formBuilder->add('price', MoneyType::class);
```

Features
--------

* Integrates money library from Mathias Verraes
* Twig filters and PHP helpers for helping with money and currencies in templates
* A storage system for currency ratios
* A ratioProvider system for fetching ratio from externals api
* Symfony form integration
* Console commands for different operations
* A configuration parser for specifying website used currencies
* Access to the history of currency ratio fetched
* Money formatter i18n

Table of contents
-----------------

* [Installation](#installation)
* [Usage](#usage)
* [Storage](#RatioStorage)
* [Contributing](#contributing)
* [Requirements](#requirements)
* [Authors](#authors)
* [Status](#status)

Installation
------------

Use [Composer](http://getcomposer.org/) and install with  
```bash
composer require tbbc/money-bundle
```

Add the bundle to config/bundles.php (if it was not automatically added during the 
installation of the package):
```php
    return [
        // ...
        Tbbc\MoneyBundle\TbbcMoneyBundle::class => ['all' => true],
    ];
```

Create a file like config/packages/tbbc_money.yml and add it there:
```yaml
tbbc_money:
    currencies: ["USD", "EUR"]
    reference_currency: "EUR"
    decimals: 2
```

In your config.yml or config/packages/tbbc_money.yml, add the form fields presentations:
```yaml
twig:
    form_themes:
        - '@TbbcMoney/Form/fields.html.twig'
```

You should also register custom Doctrine Money type:
```yaml
doctrine:
    dbal:
        types:
            money: Tbbc\MoneyBundle\Type\MoneyType
```


Usage
-----

### Money Library integration

```php
use Money\Money;

$fiveEur = Money::EUR(500);
$tenEur = $fiveEur->add($fiveEur);
[$part1, $part2, $part3] = $tenEur->allocate([1, 1, 1]);
assert($part1->equals(Money::EUR(334)));
assert($part2->equals(Money::EUR(333)));
assert($part3->equals(Money::EUR(333)));

$pair = new CurrencyPair(new Currency('EUR'), new Currency('USD'), 1.2500);
$usd = $pair->convert($tenEur);
$this->assertEquals(Money::USD(1250), $usd);
```

### Form integration

You have 3 new form types (under Tbbc\MoneyBundle\Form\Type namespace):

* CurrencyType : asks for a currency among currencies defined in config.yml
* MoneyType : asks for an amount and a currency
* SimpleMoneyType : asks for an amount and sets the currency to the reference currency set in config.yml

Example :

```php
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// I create my form
$form = $this->createFormBuilder()
    ->add('name', TextType::class)
    ->add('price', MoneyType::class, [
        'data' => Money::EUR(1000), //EUR 10
    ])
    ->add('save', SubmitType::class)
    ->getForm();
```

Manipulating the form

With `MoneyType` you can manipulate the form elements with

`amount_options` for the amount field, and `currency_options` for the currency field, fx if you want to change the label.

```php
$form = $this->createFormBuilder()
    ->add('price', MoneyType::class, [
        'data' => Money::EUR(1000), //EUR 10
        'amount_options' => [
            'label' => 'Amount',
        ],
        'currency_options' => [
            'label' => 'Currency',
        ],
    ])
    ->getForm();
```

With `CurrencyType` only `currency_options` can be used, and with `SimpleMoneyType` only `amount_options` can be used.

### Saving Money with Doctrine

#### Solution 1 : two fields in the database

Note that there are 2 columns in the DB table : $priceAmount and $priceCurrency and only one
getter/setter : getPrice and setPrice.

The get/setPrice methods are dealing with these two columns transparently.

* Advantage : your DB is clean and you can do sql sum, group by, sort,... with the amount and the currency
in two different columns in your db
* Disadvantage : it is ugly in the entity.

```php
<?php
namespace App\AdministratorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

#[ORM\Table(name: 'test_money')]
#[ORM\Entity]
class TestMoney
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id;

    #[ORM\Column]
    private int $priceAmount;

    #[ORM\Column(length: 64)]
    private string $priceCurrency;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPrice(): Money
    {
        if (!$this->priceCurrency) {
            return null;
        }
        if (!$this->priceAmount) {
            return new Money(0, new Currency($this->priceCurrency));
        }
        return new Money($this->priceAmount, new Currency($this->priceCurrency));
    }

    public function setPrice(Money $price): self
    {
        $this->priceAmount = $price->getAmount();
        $this->priceCurrency = $price->getCurrency()->getCode();

        return $this;
    }
}
```


#### Solution 2 : use Doctrine type

There is only one string column in your DB table. The money object is manually serialized by
the new Doctrine type.

1.25€ is serialized in your DB by 'EUR 125'. *This format is stable. It won't change in future releases.*.

The new Doctrine type name is "money".

* Advantage : The entity is easy to create and use
* Disadvantage : it is more difficult to directly request the db in SQL.

```php
<?php
namespace App\AdministratorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Money\Money;
 
#[ORM\Table(name: 'test_money')]
#[ORM\Entity]
class TestMoney
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id;
    
    #[ORM\Column(type: 'money')]
    private Money $price;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function setPrice(Money $price): self
    {
        $this->price = $price;
        return $this;
    }
}
```

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

### Money formatter

```php
<?php

namespace My\Controller\IndexController;

use Money\Money;
use Money\Currency;

class IndexController extends Controller
{
    public function myAction()
    {
        $moneyFormatter = $this->get('tbbc_money.formatter.money_formatter');
        $price = new Money(123456789, new Currency('EUR'));

        // best method (added in 2.2+ version)
        \Locale::setDefault('fr_FR');
        $formatedPrice = $moneyFormatter->localizedFormatMoney($price);
        // 1 234 567,89 €
        $formatedPrice = $moneyFormatter->localizedFormatMoney($price, 'en');
        // €1,234,567.89

        // old method (before v2.2)
        $formattedPrice = $moneyFormatter->formatMoney($price);
        // 1 234 567,89

        $formattedCurrency = $moneyFormatter->formatCurrency($price);
        // €
    }
}
```

### Twig integration

```twig
{{ $amount | money_localized_format('fr') }} => 1 234 567,89 €
{{ $amount | money_localized_format('en_US') }} => €1,234,567.89
{{ $amount | money_localized_format }} => depends on your default locale
{{ $amount | money_format }}
{{ $amount | money_as_float }}
{{ $amount | money_get_currency | currency_symbol }}
{{ $amount | money_get_currency | currency_name }}
{{ $amount | money_convert("USD") | money_format }}
{{ $amount | money_format_currency }}
```

### Fetching ratio values from remote provider

```bash
# save a ratio in the storage
./bin/console tbbc:money:ratio-save USD 1.25

# display ratio list
./bin/console tbbc:money:ratio-list
./bin/console tbbc:money:ratio-list --format=table
./bin/console tbbc:money:ratio-list --format=json

# fetch all the ratio for all defined currencies from an external API
./bin/console tbbc:money:ratio-fetch
```

### Change the ratio provider

The ratio provider by default is base on the service `tbbc_money.ratio_provider.ecb`.

* `tbbc_money.ratio_provider.ecb` ratio provider is based on the data provided here https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml

You can write your own ratio provider by creating and custom class that implements the `RatioProviderInterface` interface.

```php
namespace App\Money;

use Tbbc\MoneyBundle\Pair\RatioProviderInterface;

final class YourRatioProviderService implements RatioProviderInterface
{
    public function fetchRatio(string $referenceCurrencyCode, string $currencyCode): float
    {
        // implement your custom logic here
    }
}
```

You can change the service to use in the `config/packages/tbbc_money.yaml` file :

```yaml
tbbc_money:
    ratio_provider: App\Money\YourRatioProviderService
```

### Additional rate providers from Exchanger

This project integrates https://github.com/florianv/exchanger library to work with currency exchange rates from various services.

Installation: 

```bash
composer require "florianv/exchanger" "php-http/message" "php-http/guzzle7-adapter"`
```

Configuration:

First, you need to add services you would like to use into your services.yml file, e.g:
    
```yaml
ratio_provider.service.ecb:
  class: Exchanger\Service\EuropeanCentralBank
```

Second, you need to update ratio provider used by MoneyBundle on your config.yml file:

```yaml
tbbc_money:
    ratio_provider: ratio_provider.service.ecb
```

Recommended:

Some providers focus on a limited set of currencies, but give better data. 
You can use several rate providers seamlessly on your project by bundling them into the chain.
If some provider does not support certain currency, next provider in the chain would be attempted.

Example of chained providers:

```yaml
ratio_provider.service.ecb:
  class: Exchanger\Service\EuropeanCentralBank

ratio_provider.service.rcb:
  class: Exchanger\Service\RussianCentralBank

ratio_provider.service.cryptonator:
  class: Exchanger\Service\Cryptonator

ratio_provider.service.array:
  class: Exchanger\Service\PhpArray
  arguments:
    -
      'EUR/USD': 1.157
      'EUR/AUD': 1.628

ratio_provider.service.default:
  class: Exchanger\Service\Chain
  arguments:
    -
      - "@ratio_provider.service.ecb"
      - "@ratio_provider.service.rcb"
      - "@ratio_provider.service.cryptonator"
      - "@ratio_provider.service.array"
```

As you can see here 4 providers would be attempted one after another until conversion rate is found.
Check this page for a fill list of supported services and their configurations: https://github.com/florianv/exchanger/blob/master/doc/readme.md#supported-services

And then you need to assign rate provider on your config.yml file:

```
tbbc_money:
    [...]
    ratio_provider: ratio_provider.service.default
```

### Create your own ratio provider

A ratio provider is a service that implements the `Tbbc\MoneyBundle\Pair\RatioProviderInterface`.
I recommend that you read the PHP doc of the interface to understand how to implement a new ratio provider.

The new ratio provider has to be registered as a service.

To use the new ratio provider, you should set the service to use in the config.yml by giving the
service name.

```
tbbc_money:
    [...]
    ratio_provider: tbbc_money.ratio_provider.google
```



### automatic currency ratio fetch

Add to your crontab :

```
1 0 * * * /my_app_dir/bin/console tbbc:money:ratio-fetch > /dev/null
```

### MoneyManager : create a money object from a float

Create a money object from a float can be a bit tricky because of rounding issues.

```php
<?php
$moneyManager = $this->get("tbbc_money.money_manager");
$money = $moneyManager->createMoneyFromFloat('2.5', 'USD');
$this->assertEquals("USD", $money->getCurrency()->getCode());
$this->assertEquals(250, $money->getAmount());
```

### history of currency ratio with the pairHistoryManager

Doctrine is required to use this feature.

In order to get the ratio history, you have to enable it in the configuration and to use Doctrine.

```yaml
tbbc_money:
    currencies: ["USD", "EUR"]
    reference_currency: "EUR"
    enable_pair_history: true
```

Then you can use the service :

```php
$pairHistoryManager = $this->get("tbbc_money.pair_history_manager");
$dt = new \DateTime("2023-07-08 11:14:15.638276");

// returns ratio for at a given date
$ratio = $pairHistoryManager->getRatioAtDate('USD', $dt);

// returns the list of USD ratio (relative to the reference value)
$ratioList = $pairHistoryManager->getRatioHistory('USD', $startDate, $endDate);
```

RatioStorage
------------

Three storages for storing ratios are available : CSV File (csv), Doctrine ORM (doctrine), or MongoDB (document)

By default, TbbcMoneyBundle is configured with CSV File.

If you want to switch to a Doctrine storage, edit your **config.yml**

```yaml
tbbc_money:
    storage: doctrine
```

Update your database schema:

If you're using DoctrineMigrationsBundle (recommended way):
```bash
./bin/console bin/console make:migration
./bin/console bin/console doctrine:migrations:migrate
```

Without DoctrineMigrationsBundle:
```bash
./bin/console doctrine:schema:update --force
```

With the Doctrine storage, currency ratio will use the default entity manager and will store data inside the **tbbc_money_doctrine_storage_ratios**

Custom NumberFormatter in MoneyFormatter
----------------------------------------

The MoneyFormatter::localizedFormatMoney ( service 'tbbc_money.formatter.money_formatter' ) use
the php NumberFormatter class ( http://www.php.net/manual/en/numberformatter.formatcurrency.php )
to format money.

You can :

* give your own \NumberFormatter instance as a parameter of MoneyFormatter::localizedFormatMoney
* subclass the MoneyFormatter and rewrite the getDefaultNumberFormatter method to set a application wide
NumberFormatter

Using the TbbcMoneyBundle without Doctrine ORM or MongoDB
---------------------------------------------------------

You have to disable the pair history service in order to use the TbbcMoneyBundle without Doctrine ORM or MongoDB.

```yaml
tbbc_money:
    enable_pair_history: true
```

Note : you can imagine to code your own PairHistoryManager for Propel, it is very easy to do. Don't hesitate to
submit a PR with your code and your tests.

Optimizations
-------------

In your config.yml, you can :

* define the decimals count after a unit (ex : 12.25€ : 2 decimals ; 11.5678€ : 4 decimals)

```yaml
tbbc_money:
    currencies: ["USD", "EUR"]
    reference_currency: "EUR"
    decimals: 2
    enable_pair_history: true
    ratio_provider: tbbc_money.ratio_provider.yahoo_finance
```

Contributing
------------

1. Take a look at the [list of issues](https://github.com/TheBigBrainsCompany/TbbcMoneyBundle/issues).
2. Fork
3. Write a test (for either new feature or bug)
4. Make a PR

Authors
-------

Philippe Le Van - [kitpages.fr](http://www.kitpages.fr) - twitter : @plv  
Thomas Tourlourat - [Wozbe](http://wozbe.com) - twitter: @armetiz  


Status
------

Stable

what is functional:

* integration of the money library
* configuration parser
* pairManager
* Travis CI integration
* form integration
* Twig presentation for forms
* Twig filters
* commands for ratio creation and ratio display
* automatic ratio fetch (with 2 ratio providers)
* history of currency ratio
