<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Twig\Extension;

use Money\Money;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Philippe Le Van <philippe.levan@kitpages.fr>
 * @author Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class MoneyExtension extends AbstractExtension
{
    public function __construct(
        protected MoneyFormatter $moneyFormatter,
        protected PairManagerInterface $pairManager
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('money_localized_format', [$this->moneyFormatter, 'localizedFormatMoney']),
            new TwigFilter('money_format', [$this->moneyFormatter, 'formatMoney']),
            new TwigFilter('money_format_amount', [$this->moneyFormatter, 'formatAmount']),
            new TwigFilter('money_format_currency', [$this->moneyFormatter, 'formatCurrency']),
            new TwigFilter('money_as_float', [$this->moneyFormatter, 'asFloat']),
            new TwigFilter('money_get_currency', [$this->moneyFormatter, 'getCurrency']),
            new TwigFilter('money_convert', [$this, 'convert']),
        ];
    }

    /**
     * Converts the given Money object into another
     * currency and returns new Money object.
     */
    public function convert(Money $money, string $currencyCode): Money
    {
        return $this->pairManager->convert($money, $currencyCode);
    }

    public function getName(): string
    {
        return 'tbbc_money_extension';
    }
}
