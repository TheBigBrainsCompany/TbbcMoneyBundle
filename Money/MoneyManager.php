<?php
/**
 * Created by levan on 25/09/2014.
 */

namespace Tbbc\MoneyBundle\Money;


use Money\Currency;
use Money\Money;

class MoneyManager
    implements MoneyManagerInterface
{
    /** @var  int */
    protected $decimals;

    /** @var  string */
    protected $referenceCurrencyCode;

    public function __construct(
        $referenceCurrencyCode,
        $decimals = 2
    )
    {
        $this->referenceCurrencyCode = $referenceCurrencyCode;
        $this->decimals = $decimals;
    }

    /**
     * @inheritdoc
     */
    public function createMoneyFromFloat($floatAmount, $currencyCode = null)
    {
        if (is_null($currencyCode)) {
            $currencyCode = $this->referenceCurrencyCode;
        }
        $currency = new Currency($currencyCode);
        $amountAsInt = $floatAmount * pow(10, $this->decimals);
        $amountAsInt = round($amountAsInt);
        $amountAsInt = intval($amountAsInt);
        $money = new Money($amountAsInt, $currency);
        return $money;
    }

} 