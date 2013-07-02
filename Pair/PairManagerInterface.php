<?php
namespace Tbbc\MoneyBundle\Pair;

use Money\Money;

interface PairManagerInterface
{
    /**
     * convert the amount into the currencyCode given in parameter
     *
     * @param Money $amout
     * @param string $currencyCode in the list of currencies from config.yml
     * @return Money
     */
    public function convert(Money $amout, $currencyCode);

    /**
     * set ratio between the currency in parameter and the reference currency.
     *
     * @param string $currencyCode from the list of currencies
     * @param float $ratio
     */
    public function saveRatio($currencyCode, $ratio);

    /**
     * get ratio between two currencies
     *
     * @param string $referenceCurrencyCode
     * @param string $currencyCode
     * @return float
     */
    public function getRelativeRatio($referenceCurrencyCode,$currencyCode);

    /**
     * @return array of type array("EUR", "USD");
     */
    public function getCurrencyCodeList();

    /**
     * returns  currency used as reference currency
     *
     * @return string "USD"|"EUR"|...
     */
    public function getReferenceCurrencyCode();

    /**
     * return ratio list for currencies in comparison to reference currency
     *
     * @return array of type array("EUR" => 1, "USD" => 1.25);
     */
    public function getRatioList();
}