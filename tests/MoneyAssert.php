<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests;

use Money\Money;

trait MoneyAssert
{
    public static function assertMoneySame(Money $expected, Money $actual): void
    {
        self::assertSame($expected->getAmount(), $actual->getAmount());
        self::assertSame($expected->getCurrency()->getCode(), $actual->getCurrency()->getCode());
    }
}
