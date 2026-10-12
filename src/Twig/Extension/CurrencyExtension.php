<?php

declare(strict_types=1);
/**
 * Created by Philippe Le Van.
 * Date: 03/07/13.
 */

namespace Tbbc\MoneyBundle\Twig\Extension;

use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @see \Tbbc\MoneyBundle\Tests\Twig\Extension\CurrencyExtensionTest
 */
class CurrencyExtension extends AbstractExtension
{
    public function __construct(protected MoneyFormatter $moneyFormatter)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('currency_name', $this->moneyFormatter->formatCurrencyAsName(...)),
            new TwigFilter('currency_symbol', $this->moneyFormatter->formatCurrencyAsSymbol(...)),
        ];
    }

    public function getName(): string
    {
        return 'tbbc_money_currency_extension';
    }
}
