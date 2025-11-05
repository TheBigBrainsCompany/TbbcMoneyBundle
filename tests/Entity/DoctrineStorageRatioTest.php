<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Tbbc\MoneyBundle\Entity\DoctrineStorageRatio;

final class DoctrineStorageRatioTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(DoctrineStorageRatio::class));
    }

    public function testConstructor(): void
    {
        $dollar = new DoctrineStorageRatio('USD', 1.6);
        $this->assertSame('USD', $dollar->getCurrencyCode());
        $this->assertEqualsWithDelta(1.6, $dollar->getRatio(), PHP_FLOAT_EPSILON);
    }

    public function testProperties(): void
    {
        $currency = new DoctrineStorageRatio();

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
