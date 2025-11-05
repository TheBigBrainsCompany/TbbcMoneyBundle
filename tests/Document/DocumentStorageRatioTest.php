<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Document;

use PHPUnit\Framework\TestCase;
use Tbbc\MoneyBundle\Document\DocumentStorageRatio;

final class DocumentStorageRatioTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(DocumentStorageRatio::class));
    }

    public function testConstructor(): void
    {
        $dollar = new DocumentStorageRatio('USD', 1.6);
        $this->assertSame('USD', $dollar->getCurrencyCode());
        $this->assertEqualsWithDelta(1.6, $dollar->getRatio(), PHP_FLOAT_EPSILON);
    }

    public function testProperties(): void
    {
        $currency = new DocumentStorageRatio();

        $this->assertNull($currency->getId());

        $currency->setCurrencyCode('USD');
        $this->assertSame('USD', $currency->getCurrencyCode());

        $currency->setRatio(1.6);
        $this->assertEqualsWithDelta(1.6, $currency->getRatio(), PHP_FLOAT_EPSILON);

        $this->assertTrue(method_exists($currency, 'getId'));
        $this->assertTrue(method_exists($currency, 'getCurrencyCode'));
        $this->assertTrue(method_exists($currency, 'setCurrencyCode'));
        $this->assertTrue(method_exists($currency, 'getRatio'));
        $this->assertTrue(method_exists($currency, 'setRatio'));
    }
}
