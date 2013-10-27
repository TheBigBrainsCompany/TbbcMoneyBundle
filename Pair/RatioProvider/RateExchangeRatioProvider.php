<?php
/**
 * Created by Philippe Le Van.
 * Date: 12/07/13
 */

namespace Tbbc\MoneyBundle\Pair\RatioProvider;


use Money\UnknownCurrencyException;
use Money\Currency;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\RatioProviderInterface;

class RateExchangeRatioProvider
    implements RatioProviderInterface
{
    /**
     * @inheritdoc
     */
    public function fetchRatio($referenceCurrencyCode, $currencyCode)
    {
        try {
            new Currency($currencyCode);
            new Currency($referenceCurrencyCode);
        } catch (UnknownCurrencyException $e) {
            throw new MoneyException("one of your currency code doesn't exist");
        }

        $responseString = file_get_contents("http://rate-exchange.appspot.com/currency?from=$referenceCurrencyCode&to=$currencyCode");
        if (!$responseString) {
            throw new MoneyException("No response from rate-exchange API");
        }
        $response = json_decode($responseString, true);
        if (!$response) {
            throw new MoneyException("Response from rate-exchange API is not a valid JSON");
        }
        if (!array_key_exists("rate", $response)) {
            throw new MoneyException("No rate defined in the response");
        }
        $ratio = floatval($response["rate"]);
        if ($ratio <= 0) {
            throw new MoneyException("invalid ratio");
        }
        return $ratio;
    }

}