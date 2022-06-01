<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Templating\Helper;

use Money\Currency;
use Symfony\Component\Templating\Helper\Helper;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;

/**
 * Class CurrencyHelper.
 */
class CurrencyHelper extends Helper
{
    /**
     * Constructor.
     */
    public function __construct(protected MoneyFormatter $moneyFormatter)
    {
    }

    /**
     * Returns the name as string of the given currency.
     */
    public function name(Currency $currency): string
    {
        return $this->moneyFormatter->formatCurrencyAsName($currency);
    }

    /**
     * Returns the symbol corresponding to the given currency.
     */
    public function symbol(Currency $currency): string
    {
        return $this->moneyFormatter->formatCurrencyAsSymbol($currency);
    }

    public function getName(): string
    {
        return 'tbbc_money_currency_helper';
    }
}
