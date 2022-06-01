<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Money;

use Money\Currency;
use Money\Money;

/**
 * Class MoneyManager.
 *
 * @author levan
 */
class MoneyManager implements MoneyManagerInterface
{
    /**
     * MoneyManager constructor.
     */
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
        $amountAsInt = intval($amountAsInt);

        return new Money($amountAsInt, $currency);
    }
}
