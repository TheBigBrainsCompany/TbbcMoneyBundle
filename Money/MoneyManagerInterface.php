<?php
namespace Tbbc\MoneyBundle\Money;

use Money\Money;

interface MoneyManagerInterface
{
    /**
     * convert the float amount into a money object
     *
     * @param float $floatAmount amount before multiplication. Ex: 32.15
     * @param string|null $currencyCode in the list of currencies from config.yml. If null, use the reference currency
     * @return Money
     */
    public function createMoneyFromFloat($floatAmount, $currencyCode = null);

}