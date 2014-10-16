<?php
/**
 * Created by Philippe Le Van.
 * Date: 12/07/13
 */
namespace Tbbc\MoneyBundle\Pair;

/**
 * This interface is used to define the way any ratio provider has to work.
 *
 * After creating a new ratio provider, you have to register it as a service
 * and you can use it by setting the tbbc_money.ratio_provider field in the config.yml file
 */
interface RatioProviderInterface
{
    /**
     * fetch ratio from an external API.
     *
     * The returned float represents the amount in the "currency" equivalent to one unit of the "reference currency"
     *
     * ex: if EUR is the reference currency and USD is the currency, a return of 1.25 means
     * that 1 EUR = 1.25 USD (ie USD is lower than EUR)
     *
     * @param string $referenceCurrencyCode (ex: "EUR")
     * @param string $currencyCode (ex: "USD")
     * @return float
     */
    public function fetchRatio($referenceCurrencyCode, $currencyCode);

}
