<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests;

use PHPUnit\Framework\TestCase;
use Tbbc\MoneyBundle\MoneyConverter;
use Tbbc\MoneyBundle\MoneyException;

class MoneyConverterTest extends TestCase
{
    public function testCurrencyThrowExceptionOnNull(): void
    {
        $this->expectException(MoneyException::class);
        $this->expectExceptionMessage('Currency needs to be a string');
        MoneyConverter::currency(null);
    }
}
