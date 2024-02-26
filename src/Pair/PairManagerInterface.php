<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Pair;

use Money\Money;
use Tbbc\MoneyBundle\MoneyException;

/**
 * Interface PairManagerInterface.
 */
interface PairManagerInterface
{
    /**
     * convert the amount into the currencyCode given in parameter.
     */
    public function convert(Money $amount, string $currencyCode): Money;

    /**
     * set ratio between the currency in parameter and the reference currency.
     *
     * WARNING: This method has to dispatch a \TbbcMoneyEvents::AFTER_RATIO_SAVE event
     * with a SaveRatioEvent
     *
     * @throws MoneyException
     */
    public function saveRatio(string $currencyCode, float $ratio): void;

    /**
     * get ratio between two currencies.
     */
    public function getRelativeRatio(string $referenceCurrencyCode, string $currencyCode): float;

    /**
     * array of type array("EUR", "USD");.
     *
     * @return string[]
     */
    public function getCurrencyCodeList(): array;

    /**
     * returns  currency used as reference currency
     * string "USD"|"EUR"|...
     */
    public function getReferenceCurrencyCode(): string;

    /**
     * return ratio list for currencies in comparison to reference currency
     * array of type array("EUR" => 1, "USD" => 1.25);.
     *
     * @return array<string, null|float>
     */
    public function getRatioList(): array;

    /**
     * just for dependency injection. inject if needed the ratio provider.
     */
    public function setRatioProvider(RatioProviderInterface $ratioProvider): void;

    /**
     * If ratio provider is defined, get currency code list, and fetch ratio
     * from the ratio provider.
     *
     * @throws MoneyException
     */
    public function saveRatioListFromRatioProvider(): void;
}
