<?php
namespace Tbbc\MoneyBundle\Pair\RatioProvider;

use Money\Currency;
use Money\UnknownCurrencyException;
use Symfony\Component\DomCrawler\Crawler;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\RatioProviderInterface;

/**
 * ECBRatioProvider
 * Fetches currencies ratios from ECB
 * @author Johan Wilfer <johan@jttech.se>
 */
class ECBRatioProvider implements RatioProviderInterface
{
    /**
     * Fetch cache time in seconds (10 minutes)
     */
    const FETCH_CACHE_TIME = 600;

    /**
     * @var null|string Cached document from ECB to avoid fetching the same url multiple times on the same request.
     */
    protected $fetchedData;

    /**
     * @var null|integer Timestamp when we fetched the document, we will fetch it again after FETCH_CACHE_TIME
     */
    protected $fetchedTimestamp;



    /**
     * {@inheritdoc}
     */
    public function fetchRatio($referenceCurrencyCode, $currencyCode)
    {
        // we could possible take the feed and convert twice, to allow the other currencies as base currencies, but for now only allow EUR

        if ($referenceCurrencyCode !== 'EUR') {
            throw new MoneyException(sprintf('The reference currency code for ECB provider must be EUR, got: "%s"', $referenceCurrencyCode));
        }
        $baseCurrency = new Currency('EUR');

        try {
            $currency = new Currency($currencyCode);
        } catch (UnknownCurrencyException $e) {
            throw new MoneyException(
                sprintf('The currency code %s does not exists', $currencyCode)
            );
        }

        $xml = $this->getXML();
        $rates = $this->parseXML($xml);

        if (!isset($rates[$currency->getName()])) {
            throw new MoneyException(
                sprintf('The currency code %s does not exist in the ECB feed', $currencyCode)
            );
        }

        // return ratio
        $ratio = (float) $rates[$currency->getName()];

        return $ratio;
    }

    /**
     * Get the XML from ECB website - and cache it for some time
     *
     * @return null|string
     */
    protected function getXML()
    {
        // if our cached data is ok
        if ($this->fetchedTimestamp !== null && (time() < $this->fetchedTimestamp + self::FETCH_CACHE_TIME) && $this->fetchedData !== null) {
            return $this->fetchedData;
        }

        //get current exchange rate XML
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_URL, "http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
        $curlResponse = curl_exec($curlHandle);
        curl_close($curlHandle);

        // FIXME! Better error handling

        $this->fetchedData = $curlResponse;
        $this->fetchedTimestamp = time();

        return $this->fetchedData;
    }

    /**
     * Parse XML and turn it into an associative array with [ currency => rate, currency => rate, ... ]
     * @return array
     */
    protected function parseXML($xml)
    {
        $xmlObject = simplexml_load_string($xml);

        // // this wil generate a date like '2018-06-01' for the last updated date for the rates - we do not implement this now
        // $updatedDate = ((array) $xmlObject->Cube->Cube->attributes())['@attributes']['time'];

        $pairs = array();

        // @codingStandardsIgnoreLine
        foreach ($xmlObject->Cube->Cube->children() as $rateObject) {
            $attributes = (array) $rateObject->attributes();
            $pairs[$attributes['@attributes']['currency']] = $attributes['@attributes']['rate'];
        }

        return $pairs;
    }
}
