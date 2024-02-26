<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Money;

use Money\Currency;
use Money\Money;
use Tbbc\MoneyBundle\MoneyException;

/**
 * @author levan
 */
class MoneyManager implements MoneyManagerInterface
{
    public function __construct(protected string $referenceCurrencyCode, protected int $decimals = 2)
    {
        if ('' === $referenceCurrencyCode) {
            throw new MoneyException('reference currency can not be an empty string');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createMoneyFromFloat(float $floatAmount, ?string $currencyCode = null): Money
    {
        if (null === $currencyCode) {
            $currencyCode = $this->referenceCurrencyCode;
        }

        if ('' === $currencyCode) {
            throw new MoneyException('currency can not be an empty string');
        }

        $currency = new Currency($currencyCode);
        $amountAsInt = $floatAmount * 10 ** $this->decimals;
        $amountAsInt = round($amountAsInt);
        $amountAsInt = (int)$amountAsInt;

        return new Money($amountAsInt, $currency);
    }
}
