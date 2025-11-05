<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Pair\RatioProvider;

use Exception;
use Exchanger\Contract\ExchangeRateProvider;
use Exchanger\ExchangeRateQueryBuilder;
use InvalidArgumentException;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\RatioProviderInterface;

/**
 * Class ExchangerAdapterRatioProvider
 * This depends on "florianv/exchanger" package being installed.
 */
final class ExchangerAdapterRatioProvider implements RatioProviderInterface
{
    public function __construct(private ExchangeRateProvider $exchangeRateProvider)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function fetchRatio(string $referenceCurrencyCode, string $currencyCode): float
    {
        $exchangeQueryBuilder = new ExchangeRateQueryBuilder(
            $this->getCurrencyPair($referenceCurrencyCode, $currencyCode)
        );
        $exchangeQuery = $exchangeQueryBuilder->build();

        try {
            $exchangeRate = $this->exchangeRateProvider->getExchangeRate($exchangeQuery);
        } catch (Exception $e) {
            throw new MoneyException($e->getMessage());
        }

        return $exchangeRate->getValue();
    }

    private function getCurrencyPair(string $referenceCurrencyCode, string $currencyCode): string
    {
        $this->ensureValidCurrency($referenceCurrencyCode);
        $this->ensureValidCurrency($currencyCode);

        return sprintf('%s/%s', $referenceCurrencyCode, $currencyCode);
    }

    private function ensureValidCurrency(string $currencyCode): void
    {
        if ('' === $currencyCode) {
            throw new MoneyException('The currency code is an empty string');
        }

        try {
            new Currency($currencyCode);
        } catch (UnknownCurrencyException|InvalidArgumentException) {
            throw new MoneyException(sprintf('The currency code %s does not exist', $currencyCode));
        }
    }
}
