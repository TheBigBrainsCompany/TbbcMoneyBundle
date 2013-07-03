<?php
/**
 * Created by Philippe Le Van.
 * Date: 03/07/13
 */

namespace Tbbc\MoneyBundle\Twig;


use Money\Currency;
use Symfony\Component\Intl\Intl;

class CurrencyExtension
    extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('currency_name', array($this, 'name')),
            new \Twig_SimpleFilter('currency_symbol', array($this, 'symbol')),
        );
    }

    public function symbol(Currency $currency)
    {
        return Intl::getCurrencyBundle()->getCurrencySymbol($currency->getName());
    }

    public function name(Currency $currency)
    {
        return $currency->getName();
    }

    public function getName()
    {
        return 'tbbc_money_currency_extension';
    }
}