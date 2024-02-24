<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Money;

use Money\Currency;
use Money\Money;

/**
 * @author levan
 */
class MoneyManager implements MoneyManagerInterface
{
    public function __construct(protected string $referenceCurrencyCode, protected int $decimals = 2)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createMoneyFromFloat(float $floatAmount, ?string $currencyCode = null): Money
    {
        if (is_null($currencyCode)) {
            $currencyCode = $this->referenceCurrencyCode;
        }
        $currency = new Currency($currencyCode);
        $amountAsInt = $floatAmount * 10 ** $this->decimals;
        $amountAsInt = round($amountAsInt);
        $amountAsInt = (int)$amountAsInt;

        return new Money($amountAsInt, $currency);
    }
}
