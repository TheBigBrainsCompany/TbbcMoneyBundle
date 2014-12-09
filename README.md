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
* Twig filters and PHP helpers for helping with money and currencies in templates
* A storage system for currency ratios
* A ratioProvider system for fetching ratio from externals api
* Symfony2 form integration
* Console commands for different operations
* A configuration parser for specifying website used currencies
* Access to the history of currency ratio fetched
* money formatter i18n

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

in your config.yml, add the currencies you want to use and the reference currency.

```yaml
tbbc_money:
    currencies: ["USD", "EUR"]
    reference_currency: "EUR"
    decimals: 2
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

### form integration

You have 3 new form types :

* tbbc_currency : asks for a currency among currencies defined in config.yml
* tbbc_money : asks for an amount and a currency
* tbbc_simple_money : asks for an amount and sets the currency to the reference currency set in config.yml

Example :

```php
// I create my form
$form = $this->createFormBuilder($testMoney)
    ->add("name", 'text')
    ->add("price", "tbbc_money")
    ->add("save", "submit")
    ->getForm();
```


### saving moneys in doctrine

#### Solution 1 : two fields in the database

Note that there is 2 columns in db : $priceAmount and $priceCurrency and only one
getter/setter : getPrice and setPrice.

The get/setPrice are dealing with these two columns transparently.

* Advantage : your DB is clean and you can do sql sum, group by, sort,... with the amount and the currency
in two different columns in your db
* Default : it is ugly in the Entity.

```php
<?php
namespace App\AdministratorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

/**
 * TestMoney
 *
 * @ORM\Table("test_money")
 * @ORM\Entity
 */
class TestMoney
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="price_amount", type="integer")
     */
    private $priceAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="price_currency", type="string", length=64)
     */
    private $priceCurrency;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * get Money
     *
     * @return Money
     */
    public function getPrice()
    {
        if (!$this->priceCurrency) {
            return null;
        }
        if (!$this->priceAmount) {
            return new Money(0, new Currency($this->priceCurrency));
        }
        return new Money($this->priceAmount, new Currency($this->priceCurrency));
    }

    /**
     * Set price
     *
     * @param Money $price
     * @return TestMoney
     */
    public function setPrice(Money $price)
    {
        $this->priceAmount = $price->getAmount();
        $this->priceCurrency = $price->getCurrency()->getName();

        return $this;
    }
}
```


#### Solution 2 : use doctrine type

There is only one string column in your DB. The money object is manually serialized by
the new doctrine type.

1.25€ is serialized in your DB by 'EUR 125'. *This format is stable. It won't change in future releases.*.

The new doctrine type name is "money".

* Advantage : The entity is easy to create and use
* Default : it is more difficult to directly request de db in SQL.

```php
<?php
namespace App\AdministratorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * TestMoney
 *
 * @ORM\Table("test_money")
 * @ORM\Entity
 */
class TestMoney
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Money
     *
     * @ORM\Column(name="price", type="money")
     */
    private $price;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * get Money
     *
     * @return Money
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set price
     *
     * @param Money $price
     * @return TestMoney
     */
    public function setPrice(Money $price)
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

### PHP templating integration

```php
<span class="price"><?php echo $view['tbbc_money']->format($price) ?></span>
<span class="money"><?php echo $view['tbbc_money_currency']->formatCurrencyAsSymbol($price->getCurrency()) ?></span>
```

### Fetching ratio values from remote provider

```bash
# save a ratio in the storage
./app/console tbbc:money:ratio-save USD 1.25

# display ratio list
./app/console tbbc:money:ratio-list

# fetch all the ratio for all defined currencies from an external API
./app/console tbbc:money:ratio-fetch
```

### Change the ratio provider

The ratio provider by default is base on the service 'tbbc_money.ratio_provider.rate_exchange'

This bundles contains two ratio providers :

* tbbc_money.ratio_provider.rate_exchange based on the http://rate-exchange.appspot.com/ service
* tbbc_money.ratio_provider.google based on the https://www.google.com/finance/converter service

You can change the service to use in the config.yml file :

```
tbbc_money:
    [...]
    ratio_provider: tbbc_money.ratio_provider.google
```


### Create your own ratio provider

A ratio provider is a service that implements the Tbbc\MoneyBundle\Pair\RatioProviderInterface.
I let you read the PHP doc of the interface to understand how to implement a new ratio provider.

The the new ratio provider has to be registered as a service.

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
1 0 * * * /my_app_dir/app/console tbbc:money:ratio-fetch > /dev/null
```

### MoneyManager : create a money from a float

Create a money from a float can be a bit tricky because of rounding issues.

```php
<?php
$moneyManager = $this->get("tbbc_money.money_manager");
$money = $moneyManager->createMoneyFromFloat('2.5', 'USD');
$this->assertEquals("USD", $money->getCurrency()->getName());
$this->assertEquals(250, $money->getAmount());
```

### history of currency ratio with the pairHistoryManager

Doctrine is required to use this feature.

In order to get the ratio history, you have to enable it in the configuration and to use doctrine.

```yaml
tbbc_money:
    currencies: ["USD", "EUR"]
    reference_currency: "EUR"
    enable_pair_history: true
```

then you can use the service :

```php
$pairHistoryManager = $this->get("tbbc_money.pair_history_manager");
$dt = new \DateTime("2012-07-08 11:14:15.638276");

// returns ratio for at a given date
$ratio = $pairHistoryManager->getRatioAtDate('USD',$dt);

// returns the list of USD ratio (relative to the reference value)
$ratioList = $pairHistoryManager->getRatioHistory('USD',$startDate, $endDate);
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

Custom NumberFormatter in MoneyFormatter
----------------------------------------

The MoneyFormatter::localizedFormatMoney ( service 'tbbc_money.formatter.money_formatter' ) use
the php NumberFormatter class ( http://www.php.net/manual/en/numberformatter.formatcurrency.php )
to format money.

You can :

* give your own \NumberFormatter instance as a parameter of MoneyFormatter::localizedFormatMoney
* subclass the MoneyFormatter and rewrite the getDefaultNumberFormater method to set a application wide
NumberFormatter

Using the TbbcMoneyBundle without doctrine
------------------------------------------

You have to disable the pair history service in order to use the TbbcMoneyBundle without doctrine.

```
tbbc_money:
    enable_pair_history: true
```

Note : you can imagine to code your own PairHistoryManager for mongodb or propel, it is very easy to do. Don't
hesitate to submit a PR with your code and your tests.

Optimizations
-------------

in your config.yml, you can :

* select the templating engine to use. By default, only twig is loaded.
* define the decimals count after a unit (ex : 12.25€ : 2 decimals ; 11.5678€ : 4 decimals)

```yaml
tbbc_money:
    currencies: ["USD", "EUR"]
    reference_currency: "EUR"
    decimals: 2
    enable_pair_history: true
    ratio_provider: tbbc_money.ratio_provider.rate_exchange
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
* automatic ratio fetch (with 2 ratio providers)
* history of currency ratio


Versions
--------

2.6.0 : 2014/12/09

* no BC Break
* new : removed dependency to doctrine
* fix : currencies with 0 decimals are allowed 

2.5.0 : 2014/10/16

* no BC Break
* new : A brand new ratio provider based on google finance convert
* new : a simple way to choose the ratio provider from the config.yml
* doc : readme updated
* doc : better phpdoc for the RatioProviderInterface.php

2.4.0 : 2014/09/25

* no BC Break
* major update in form types : form types are injected by names so extensions are invoked
* new : a brand new MoneyManager that allows to create a Money object from a float value
* doc : small fixes

2.3.1 : 2014/07/16

* no BC Break
* fix : history or ratio was saved more than once
* fix : history of the reference currency returns a 1 ratio

2.3.0 : 2014/07/11

* no BC Break
* new : history of currency ratio saved

2.2.0 : 2014/07/11

* no BC Break
* New : new formater MoneyFormatter::localizedFormatMoney that displays money with PHP
NumberFormatter class : http://www.php.net/manual/en/numberformatter.formatcurrency.php
* new : twig filter money_localized_format

2.1.1 : 2014/06/02

* no BC Break
* fix access to multiple entity manager : PR : https://github.com/TheBigBrainsCompany/TbbcMoneyBundle/pull/17
* fix updating multiple currencies : PR : https://github.com/TheBigBrainsCompany/TbbcMoneyBundle/pull/18
* thx Xobb on github for it's PR

2.1.0 : 2014/02/01

* no BC Break
* new parameter : decimal count in config.yml for number of decimals (for every money)

2.0.1 : 2013/12/18

* only README fixes

2.0.0 : 2013/12/17

* BC Break : Twig/MoneyExtension moved to Twig/Extension/MoneyExtension
* BC Break : Twig/Extension/MoneyExtension changed it's internals => possible BC Break in
* new : separation between formaters and twig extension

1.5.0 : 2013/12/17

* fix : use statement missing in exception case

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


