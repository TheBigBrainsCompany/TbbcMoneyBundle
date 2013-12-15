<?php

namespace Tbbc\MoneyBundle\Templating\Helper;

use Money\Money;
use Symfony\Component\Templating\Helper\Helper;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

class MoneyHelper extends Helper
{
    /**
     * @var MoneyFormatter
     */
    protected $moneyFormatter;

    /**
     * @var PairManagerInterface
     */
    protected $pairManager;

    /**
     * Constructor
     *
     * @param MoneyFormatter       $moneyFormatter
     * @param PairManagerInterface $pairManager
     */
    public function __construct(MoneyFormatter $moneyFormatter, PairManagerInterface $pairManager)
    {
        $this->moneyFormatter = $moneyFormatter;
        $this->pairManager = $pairManager;
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
    public function format(Money $money, $decPoint = ',', $thousandsSep = ' ')
    {
        return $this->moneyFormatter->formatMoney($money, $decPoint, $thousandsSep);
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
        return $this->moneyFormatter->formatAmount($money, $decPoint, $thousandsSep);
    }

    /**
     * Returns the amount for the given Money object as simple float
     *
     * @param Money $money
     * @return float
     */
    public function asFloat(Money $money)
    {
        return $this->moneyFormatter->asFloat($money);
    }

    /**
     * Formats only the currency part of the given Money object
     *
     * @param Money $money
     * @return string
     */
    public function formatCurrency($money)
    {
        return $this->moneyFormatter->formatCurrency($money);
    }

    /**
     * Returns the Currency object
     *
     * @param Money $money
     * @return \Money\Currency
     */
    public function getCurrency(Money $money)
    {
        return $this->moneyFormatter->getCurrency($money);
    }

    /**
     * Converts the given Money object into another
     * currency and returns a new Money object
     *
     * @param Money  $money
     * @param string $currencyCode
     * @return Money
     */
    public function convert(Money $money, $currencyCode)
    {
        return $this->pairManager->convert($money, $currencyCode);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'tbbc_money_helper';
    }
}
