<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Templating\Helper;

use Money\Currency;
use Money\Money;
use Symfony\Component\Templating\Helper\Helper;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

/**
 * Class MoneyHelper.
 */
class MoneyHelper extends Helper
{
    public function __construct(protected MoneyFormatter $moneyFormatter, protected PairManagerInterface $pairManager)
    {
    }

    /**
     * Formats the given Money object
     * INCLUDING the currency symbol.
     */
    public function format(Money $money, string $decPoint = ',', string $thousandsSep = ' '): string
    {
        return $this->moneyFormatter->formatMoney($money, $decPoint, $thousandsSep);
    }

    /**
     * Formats the amount part of the given Money object
     * WITHOUT INCLUDING the currency symbol.
     */
    public function formatAmount(Money $money, string $decPoint = ',', string $thousandsSep = ' '): string
    {
        return $this->moneyFormatter->formatAmount($money, $decPoint, $thousandsSep);
    }

    /**
     * Returns the amount for the given Money object as simple float.
     */
    public function asFloat(Money $money): float
    {
        return $this->moneyFormatter->asFloat($money);
    }

    /**
     * Formats only the currency part of the given Money object.
     */
    public function formatCurrency(Money $money): string
    {
        return $this->moneyFormatter->formatCurrency($money);
    }

    /**
     * Returns the Currency object.
     */
    public function getCurrency(Money $money): Currency
    {
        return $this->moneyFormatter->getCurrency($money);
    }

    /**
     * Converts the given Money object into another
     * currency and returns new Money object.
     */
    public function convert(Money $money, string $currencyCode): Money
    {
        return $this->pairManager->convert($money, $currencyCode);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'tbbc_money_helper';
    }
}
