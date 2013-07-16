<?php
/**
 * Created by Philippe Le Van.
 * Date: 12/07/13
 */
namespace Tbbc\MoneyBundle\Pair;


interface RatioProviderInterface
{
    /**
     * fetch ratio from an external API
     *
     * @param string $referenceCurrencyCode (ex: "EUR")
     * @param string $currencyCode (ex: "USD")
     * @return float
     */
    public function fetchRatio($referenceCurrencyCode, $currencyCode);

}
