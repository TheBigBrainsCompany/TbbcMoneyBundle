<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Formatter;

use Money\Currency;
use Money\Money;

/**
 * Money formatter.
 *
 * @author Benjamin Dulau <benjamin@thebigbrainscompany.com>
 */
class MoneyFormatter
{
    public function __construct(protected int $decimals = 2)
    {
    }

    /**
     * Format money with the NumberFormatter class.
     *
     * You can force the locale or event use your own $numberFormatter instance to format
     * the output as you wish.
     *
     * @see http://www.php.net/manual/en/numberformatter.formatcurrency.php
     */
    public function localizedFormatMoney(Money $money, ?string $locale = null, ?\NumberFormatter $numberFormatter = null): string
    {
        if (!($numberFormatter instanceof \NumberFormatter)) {
            $numberFormatter = $this->getDefaultNumberFormatter($money->getCurrency()->getCode(), $locale);
        }

        return $numberFormatter->formatCurrency($this->asFloat($money), $money->getCurrency()->getCode());
    }

    /**
     * Formats the given Money object
     * INCLUDING the currency symbol.
     */
    public function formatMoney(Money $money, string $decPoint = ',', string $thousandsSep = ' '): string
    {
        $symbol = $this->formatCurrency($money);
        $amount = $this->formatAmount($money, $decPoint, $thousandsSep);

        return $amount . ' ' . $symbol;
    }

    /**
     * Formats the amount part of the given Money object
     * WITHOUT INCLUDING the currency symbol.
     */
    public function formatAmount(Money $money, string $decPoint = ',', string $thousandsSep = ' '): string
    {
        $amount = $this->asFloat($money);

        return number_format($amount, $this->decimals, $decPoint, $thousandsSep);
    }

    /**
     * Returns the amount for the given Money object as simple float.
     */
    public function asFloat(Money $money): float
    {
        $amount = $money->getAmount();
        $amount = (float) $amount;

        return $amount / 10 ** $this->decimals;
    }

    /**
     * Formats only the currency part of the given Money object.
     */
    public function formatCurrency(Money $money): string
    {
        return $this->formatCurrencyAsSymbol($money->getCurrency());
    }

    /**
     * Returns the symbol corresponding to the given currency.
     */
    public function formatCurrencyAsSymbol(Currency $currency): string
    {
        // @todo make sure this returns the correct thing
        $formatter = new \NumberFormatter(
            sprintf('en-US@currency=%s', $this->formatCurrencyAsName($currency)),
            \NumberFormatter::CURRENCY
        );

        return $formatter->getSymbol(\NumberFormatter::CURRENCY_SYMBOL);
    }

    /**
     * Returns the name as string of the given currency.
     */
    public function formatCurrencyAsName(Currency $currency): string
    {
        return $currency->getCode();
    }

    /**
     * Returns the Currency object.
     */
    public function getCurrency(Money $money): Currency
    {
        return $money->getCurrency();
    }

    protected function getDefaultNumberFormatter(string $currencyCode, ?string $locale = null): \NumberFormatter
    {
        if (is_null($locale)) {
            $locale = \Locale::getDefault();
        }

        $numberFormatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        $numberFormatter->setTextAttribute(\NumberFormatter::CURRENCY_CODE, $currencyCode);
        $numberFormatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $this->decimals);

        return $numberFormatter;
    }
}
