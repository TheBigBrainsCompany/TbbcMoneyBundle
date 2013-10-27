<?php
/**
 * Created by Philippe Le Van.
 * Date: 03/07/13
 */

namespace Tbbc\MoneyBundle\Twig;

use Money\Money;
use Symfony\Component\Intl\Intl;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

/**
 * @author Philippe Le Van <philippe.levan@kitpages.fr>
 * @author Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class MoneyExtension extends \Twig_Extension
{
    /**
     * @var PairManagerInterface
     */
    protected $pairManager;

    /**
     * Constructor
     *
     * @param PairManagerInterface $pairManager
     */
    public function __construct(PairManagerInterface $pairManager)
    {
        $this->pairManager = $pairManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('money_format', array($this, 'format')),
            new \Twig_SimpleFilter('money_format_amount', array($this, 'formatAmount')),
            new \Twig_SimpleFilter('money_format_currency', array($this, 'formatCurrency')),
            new \Twig_SimpleFilter('money_as_float', array($this, 'asFloat')),
            new \Twig_SimpleFilter('money_get_currency', array($this, 'getCurrency')),
            new \Twig_SimpleFilter('money_convert', array($this, 'convert')),
        );
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
        $amount = $money->getAmount();
        $amount = (float)$amount;
        $amount = $amount / 100;
        $amount = number_format($amount, 2, $decPoint, $thousandsSep);

        return $amount;
    }

    /**
     * Formats ONLY the currency part of the given Money object
     * into a localized string
     *
     * @param Money $money
     * @return null|string
     */
    public function formatCurrency(Money $money)
    {
        return Intl::getCurrencyBundle()->getCurrencySymbol($money->getCurrency()->getName());
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
        $amount = $amount / 100;

        return $amount;
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
        return 'tbbc_money_extension';
    }
}
