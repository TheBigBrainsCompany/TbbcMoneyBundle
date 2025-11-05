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
 * @author Philippe Le Van <philippe.levan@kitpages.fr>
 * @author Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class CurrencyExtension extends AbstractExtension
{
    public function __construct(protected MoneyFormatter $moneyFormatter)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('currency_name', [$this->moneyFormatter, 'formatCurrencyAsName']),
            new TwigFilter('currency_symbol', [$this->moneyFormatter, 'formatCurrencyAsSymbol']),
        ];
    }

    public function getName(): string
    {
        return 'tbbc_money_currency_extension';
    }
}
