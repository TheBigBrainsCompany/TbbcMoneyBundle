<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Pair\RatioProvider;

use InvalidArgumentException;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\RatioProviderInterface;

/**
 * ECBRatioProvider
 * Fetches currencies ratios from ECB.
 *
 * @author Johan Wilfer <johan@jttech.se>
 */
class ECBRatioProvider implements RatioProviderInterface
{
    /**
     * Fetch cache time in seconds (10 minutes).
     */
    public const FETCH_CACHE_TIME = 600;
    private const URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

    /**
     * Cached document from ECB to avoid fetching the same url multiple times on the same request.
     */
    protected ?string $fetchedData = null;

    /**
     * Timestamp when we fetched the document, we will fetch it again after FETCH_CACHE_TIME.
     */
    protected ?int $fetchedTimestamp = null;

    public function __construct(private HttpClientInterface $client)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRatio(string $referenceCurrencyCode, string $currencyCode): float
    {
        // we could possibly take the feed and convert twice, to allow the other currencies as base currencies, but for now only allow EUR

        if ('EUR' !== $referenceCurrencyCode) {
            throw new MoneyException(sprintf('The reference currency code for ECB provider must be EUR, got: "%s"', $referenceCurrencyCode));
        }

        if ('' === $currencyCode) {
            throw new MoneyException('The currency code is an empty string');
        }

        try {
            $currency = new Currency($currencyCode);
        } catch (UnknownCurrencyException|InvalidArgumentException) {
            throw new MoneyException(sprintf('The currency code "%s" does not exists', $currencyCode));
        }

        $xml = $this->getXML();
        if (null === $xml || '' === $xml) {
            throw new MoneyException('Could not fetch XML from ECB');
        }

        $rates = $this->parseXML($xml);

        if (!isset($rates[$currency->getCode()])) {
            throw new MoneyException(sprintf('The currency code %s does not exist in the ECB feed', $currencyCode));
        }

        return (float) $rates[$currency->getCode()];
    }

    /**
     * Get the XML from ECB website - and cache it for some time.
     */
    protected function getXML(): ?string
    {
        // if our cached data is ok
        if (null !== $this->fetchedTimestamp && (time() < $this->fetchedTimestamp + self::FETCH_CACHE_TIME) && null !== $this->fetchedData) {
            return $this->fetchedData;
        }

        $response = $this->client->request('GET', self::URL);
        if (200 !== $response->getStatusCode()) {
            throw new MoneyException(sprintf('The request to %s failed with status code %d', self::URL, $response->getStatusCode()));
        }

        $this->fetchedTimestamp = time();
        $this->fetchedData = $response->getContent();

        return $this->fetchedData;
    }

    /**
     * Parse XML and turn it into an associative array with [ currency => rate, currency => rate, ... ].
     *
     * @psalm-suppress all
     */
    protected function parseXML(string $xml): array
    {
        if (!$xmlObject = @simplexml_load_string($xml)) {
            throw new MoneyException('Failed to parse XML from ECB');
        }

        // // this wil generate a date like '2018-06-01' for the last updated date for the rates - we do not implement this now
        // $updatedDate = ((array) $xmlObject->Cube->Cube->attributes())['@attributes']['time'];

        $pairs = [];

        foreach ($xmlObject->Cube->Cube->children() as $rateObject) {
            // @codeCoverageIgnoreStart
            if (null === $rateObject) {
                continue;
            }
            // @codeCoverageIgnoreEnd

            $attributes = (array) $rateObject->attributes();
            $pairs[$attributes['@attributes']['currency']] = $attributes['@attributes']['rate'];
        }

        return $pairs;
    }
}
