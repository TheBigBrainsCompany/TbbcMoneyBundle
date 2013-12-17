<?php
/**
 * Created by Philippe Le Van.
 * Date: 03/07/13
 */

namespace Tbbc\MoneyBundle\Twig\Extension;

use Tbbc\MoneyBundle\Formatter\MoneyFormatter;

/**
 * @author Philippe Le Van <philippe.levan@kitpages.fr>
 * @author Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class CurrencyExtension extends \Twig_Extension
{
    /**
     * @var MoneyFormatter
     */
    protected $moneyFormatter;

    /**
     * Constructor
     *
     * @param MoneyFormatter $moneyFormatter
     */
    public function __construct(MoneyFormatter $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('currency_name', array($this->moneyFormatter, 'formatCurrencyAsName')),
            new \Twig_SimpleFilter('currency_symbol', array($this->moneyFormatter, 'formatCurrencyAsSymbol')),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'tbbc_money_currency_extension';
    }
}