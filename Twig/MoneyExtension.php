<?php
/**
 * Created by Philippe Le Van.
 * Date: 03/07/13
 */

namespace Tbbc\MoneyBundle\Twig;


use Money\Money;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Intl\ResourceBundle\CurrencyBundle;
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
        $symbol = Intl::getCurrencyBundle()->getCurrencySymbol($money->getCurrency()->getName());

        $amount = $money->getAmount();
        $amount = (float)$amount;
        $amount = $amount / 100;
        $price = number_format($amount, 2, $decPoint, $thousandsSep);
        $price = $price." ".$symbol;
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
        return $money->getCurrency();
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