<?php

namespace Tbbc\MoneyBundle\Formatter;

use Money\Currency;
use Money\Money;
use Symfony\Component\Intl\Intl;

/**
 * Money formatter
 *
 * @author Benjamin Dulau <benjamin@thebigbrainscompany.com>
 */
class MoneyFormatter
{
    protected $decimals;

    public function __construct($decimals = 2)
    {
        $this->decimals = $decimals;
    }

    /**
     * Formats the given Money object
     * INCLUDING the currency symbol
     *
     * @param Money  $money
     * @param string $decPoint
     * @param string $thousandsSep
     *
     * @return string
     */
    public function formatMoney(Money $money, $decPoint = ',', $thousandsSep = ' ')
    {
        $symbol = $this->formatCurrency($money);
        $amount = $this->formatAmount($money, $decPoint, $thousandsSep);
        $price = $amount . " " . $symbol;

        return $price;
    }

    /**
     * Formats the amount part of the given Money object
     * WITHOUT INCLUDING the currency symbol
     *
     * @param Money  $money
     * @param string $decPoint
     * @param string $thousandsSep
     *
     * @return string
     */
    public function formatAmount(Money $money, $decPoint = ',', $thousandsSep = ' ')
    {
        $amount = $this->asFloat($money);
        $amount = number_format($amount, $this->decimals, $decPoint, $thousandsSep);

        return $amount;
    }

    /**
     * Returns the amount for the given Money object as simple float
     *
     * @param Money $money
     * @return float
     */
    public function asFloat(Money $money)
    {
        $amount = $money->getAmount();
        $amount = (float)$amount;
        $amount = $amount / pow(10, $this->decimals);

        return $amount;
    }

    /**
     * Formats only the currency part of the given Money object
     *
     * @param Money $money
     * @return string
     */
    public function formatCurrency(Money $money)
    {
        return $this->formatCurrencyAsSymbol($money->getCurrency());
    }

    /**
     * Returns the symbol corresponding to the given currency
     *
     * @param Currency $currency
     * @return string
     */
    public function formatCurrencyAsSymbol(Currency $currency)
    {
        return Intl::getCurrencyBundle()->getCurrencySymbol($currency->getName());
    }

    /**
     * Returns the name as string of the given currency
     *
     * @param Currency $currency
     * @return string
     */
    public function formatCurrencyAsName(Currency $currency)
    {
        return $currency->getName();
    }

    /**
     * Returns the Currency object
     *
     * @param Money $money
     * @return \Money\Currency
     */
    public function getCurrency(Money $money)
    {
        return $money->getCurrency();
    }
}
