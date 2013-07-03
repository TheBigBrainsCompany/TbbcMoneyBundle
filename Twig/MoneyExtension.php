<?php
/**
 * Created by Philippe Le Van.
 * Date: 03/07/13
 */

namespace Tbbc\MoneyBundle\Twig;


use Money\Money;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

class MoneyExtension
    extends \Twig_Extension
{
    /** @var PairManagerInterface  */
    protected $pairManager;

    public function __construct(PairManagerInterface $pairManager)
    {
        $this->pairManager = $pairManager;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('money_format', array($this, 'format')),
            new \Twig_SimpleFilter('money_as_float', array($this, 'asFloat')),
            new \Twig_SimpleFilter('money_get_currency', array($this, 'getCurrency')),
            new \Twig_SimpleFilter('money_convert', array($this, 'convert')),
        );
    }

    public function format(Money $money, $decPoint = ',', $thousandsSep = ' ')
    {
        $amount = $money->getAmount();
        $amount = (float)$amount;
        $amount = $amount / 100;
        $price = number_format($amount, 2, $decPoint, $thousandsSep);
        $price = $price." ".$money->getCurrency()->getName();
        return $price;
    }

    public function asFloat(Money $money)
    {
        $amount = $money->getAmount();
        $amount = (float)$amount;
        $amount = $amount / 100;
        return $amount;
    }

    public function getCurrency(Money $money)
    {
        return $money->getCurrency()->getName();
    }

    public function convert(Money $money, $currencyCode)
    {
        return $this->pairManager->convert($money,$currencyCode);
    }

    public function getName()
    {
        return 'tbbc_money_extension';
    }
}