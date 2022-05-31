<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Money;

use Money\Money;

/**
 * Interface MoneyManagerInterface.
 */
interface MoneyManagerInterface
{
    /**
     * convert the float amount into money object.
     */
    public function createMoneyFromFloat(float $floatAmount, ?string $currencyCode = null): Money;
}
