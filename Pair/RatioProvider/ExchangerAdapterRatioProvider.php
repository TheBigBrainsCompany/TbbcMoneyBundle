<?php
namespace Tbbc\MoneyBundle\Pair\RatioProvider;

use Money\Currency;
use Money\UnknownCurrencyException;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\RatioProviderInterface;
use Exchanger\CurrencyPair;
use Exchanger\ExchangeRateQueryBuilder;
use Exchanger\Contract\ExchangeRateProvider;

/**
 * Class ExchangerAdapterRatioProvider
 * This depends on "florianv/exchanger" package being installed
 *
 * @package Tbbc\MoneyBundle\Pair
 *
 * This adapter takes Exchanger Rate Provider as an input and fetches rates in format suitable for RatioProviderInterface
 */
final class ExchangerAdapterRatioProvider implements RatioProviderInterface
{
    /**
     * @var ExchangeRateProvider
     */
    private $exchangeRateProvider;

    /**
     * SwapAdapterRatioProvider constructor.
     *
     * @param ExchangeRateProvider $exchangeRateProvider
     */
    public function __construct(ExchangeRateProvider $exchangeRateProvider)
    {
        $this->exchangeRateProvider = $exchangeRateProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRatio($referenceCurrencyCode, $currencyCode)
    {
        $exchangeQueryBuilder = new ExchangeRateQueryBuilder(
            $this->getCurrencyPair($referenceCurrencyCode, $currencyCode)
        );
        $exchangeQuery = $exchangeQueryBuilder->build();

        try {
            $exchangeRate = $this->exchangeRateProvider->getExchangeRate($exchangeQuery);
        } catch (\Exception $e) {
            throw new MoneyException($e->getMessage());
        }

        return (float) $exchangeRate->getValue();
    }

    /**
     * @param string $referenceCurrencyCode
     * @param string $currencyCode
     *
     * @return CurrencyPair
     */
    private function getCurrencyPair($referenceCurrencyCode, $currencyCode)
    {
        $this->ensureValidCurrency($referenceCurrencyCode);
        $this->ensureValidCurrency($currencyCode);

        return new CurrencyPair($referenceCurrencyCode, $currencyCode);
    }

    /**
     * @param string $currencyCode
     *
     * @return Currency
     * @throws MoneyException
     */
    private function ensureValidCurrency($currencyCode)
    {
        try {
            return new Currency($currencyCode);
        } catch (UnknownCurrencyException $e) {
            throw new MoneyException(
                sprintf('The currency code %s does not exist', $currencyCode)
            );
        }
    }
}
