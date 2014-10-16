<?php

namespace Tbbc\MoneyBundle\Pair\RatioProvider;

use Money\Currency;
use Money\UnknownCurrencyException;
use Symfony\Component\DomCrawler\Crawler;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\RatioProviderInterface;

/**
 * GoogleRatioProvider
 * Fetches currencies ratios from google finance currency converter
 * @author Hugues Maignol <hugues.maignol@kitpages.fr>
 */
class GoogleRatioProvider implements RatioProviderInterface
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

        $baseUnits = 1000;
        $endpoint = $this->getEndpoint($baseUnits, $baseCurrency, $currency);
        $responseString = file_get_contents($endpoint);
        $convertedAmount = $this->getConvertedAmountFromResponse($responseString);
        $ratio = $convertedAmount / $baseUnits;

        return $ratio;
    }

    /**
     * @param          $units
     * @param Currency $referenceCurrency
     * @param Currency $currency
     * @return string The endpoint to get Currency conversion
     */
    protected function getEndpoint($units, Currency $referenceCurrency, Currency $currency)
    {
        return sprintf(
            'https://www.google.com/finance/converter?a=%s&from=%s&to=%s',
            $units,
            $referenceCurrency->getName(),
            $currency->getName()
        );
    }

    /**
     * @param string $response
     * @throws MoneyException
     * @return float The converted Amount
     */
    protected function getConvertedAmountFromResponse($response)
    {
        $crawler = new Crawler($response);
        $rawConvertedAmount = $crawler->filterXPath('//div[@id="currency_converter_result"]/span[@class="bld"]')->text();
        $floatConvertedAmount = (float)$rawConvertedAmount;

        if (! $rawConvertedAmount || $floatConvertedAmount <= 0) {
            throw new MoneyException("Cannot parse response from google finance converter API");
        }

        return $floatConvertedAmount;
    }
}
