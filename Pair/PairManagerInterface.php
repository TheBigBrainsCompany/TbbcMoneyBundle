<?php
namespace Tbbc\MoneyBundle\Pair;

use Money\Currency;
use Money\Money;

interface PairManagerInterface
{
    /**
     * convert the amount into the currencyCode given in parameter
     *
     * @param Money $amount
     * @param string $currencyTo in the list of currencies from config.yml
     * @return Money
     */
    public function convert(Money $amount, $currencyTo);

    /**
     * set ratio between the currency in parameter and the reference currency.
     *
     * WARNING: This method has to dispatch a \TbbcMoneyEvents::AFTER_RATIO_SAVE event
     * with a SaveRatioEvent
     *
     * @param string|Currency $currencyTo
     * @param float $ratio
     * @param string|Currency|null $currencyFrom if null - reference currency will be used
     * @return
     */
    public function saveRatio($currencyTo, $ratio, $currencyFrom = null);

    /**
     * get ratio between two currencies
     *
     * @param string|Currency $currencyFrom
     * @param string|Currency $currencyTo
     * @param boolean $isStrict if true - tries only pairs from storage. Else - works with reference currency as intermediate exchange currency
     * @return float
     */
    public function getRelativeRatio($currencyFrom, $currencyTo, $isStrict = false);

    /**
     * @return array of type array("EUR", "USD");
     */
    public function getCurrencyCodeList();

    /**
     * returns  currency code used as reference currency
     *
     * @return string "USD"|"EUR"|...
     */
    public function getReferenceCurrencyCode();

    /**
     * returns  currency used as reference currency
     *
     * @return Currency
     */
    public function getReferenceCurrency();

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