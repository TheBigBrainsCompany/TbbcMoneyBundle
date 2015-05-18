<?php

namespace Tbbc\MoneyBundle\Pair\RatioProvider;

use Money\Currency;
use Money\UnknownCurrencyException;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\RatioProviderInterface;

/**
 * GoogleRatioProvider
 * Fetches currencies ratios from google finance currency converter.
 *
 * @author Hugues Maignol <hugues.maignol@kitpages.fr>
 */
class YahooFinanceRatioProvider implements RatioProviderInterface
{
    /**
     * @inheritdoc
     */
    public function fetchRatio($referenceCurrencyCode, $currencyCode)
    {
        try {
            $baseCurrency = new Currency($referenceCurrencyCode);
        } catch (UnknownCurrencyException $e) {
            throw new MoneyException(
                sprintf('The currency code %s does not exists', $referenceCurrencyCode)
            );
        }
        try {
            $currency = new Currency($currencyCode);
        } catch (UnknownCurrencyException $e) {
            throw new MoneyException(
                sprintf('The currency code %s does not exists', $currencyCode)
            );
        }

        $responseContent = $this->executeQuery($this->getEndpoint($baseCurrency, $currency));
        $ratio = $this->getRatioFromResponse($responseContent);

        return $ratio;
    }

    /**
     * Executes the query to the API endpoint.
     *
     * @param string $endpoint Endpoint's URI
     *
     * @return string The response content
     *
     * @throws MoneyException if the query has failed or the response status is not 200
     */
    protected function executeQuery($endpoint)
    {
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $responseContent = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($responseContent === false || $httpStatus != 200) {
            throw new MoneyException('Could not execute YahooFinanceRatioProvider query to endpoint '.$endpoint);
        }

        curl_close($ch);

        return $responseContent;
    }

    /**
     * @param Currency $referenceCurrency
     * @param Currency $currency
     *
     * @return string The yahoo finance endpoint to get Currency exchange rate
     */
    protected function getEndpoint(Currency $referenceCurrency, Currency $currency)
    {
        return
            'https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20in%20(%22'.
            $referenceCurrency->getName().
            $currency->getName().
            '%22)&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys';
    }

    /**
     * @param string $response The json response of the YahooFinance Api
     *
     * @return float The current exchange rate between currencies
     *
     * @throws MoneyException If no rate can be extracted from the response
     */
    protected function getRatioFromResponse($response)
    {
        $content = json_decode($response);
        $rate = $content
            ->query
            ->results
            ->rate
            ->Rate;
        if ($rate == 'N/A') {
            $pair = $content->query->results->rate->id;
            throw new MoneyException('YahooFinanceApi does not have an exchange rate for '.$pair);
        }
        $ratio = (float) $rate;

        return $ratio;
    }
}
