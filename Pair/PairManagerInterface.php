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
     * WARNING: This method has to dispatch a \TbbcMoneyEvents::AFTER_RATIO_SAVE event
     * with a SaveRatioEvent
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

    /**
     * just for dependency injection. inject if needed the ratio provider
     *
     * @param RatioProviderInterface $ratioProvider
     */
    public function setRatioProvider(RatioProviderInterface $ratioProvider);

    /**
     * If ratio provider is defined, get currency code list, and fetch ratio
     * from the ratio provider.
     */
    public function saveRatioListFromRatioProvider();
}