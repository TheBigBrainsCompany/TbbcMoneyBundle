<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Tbbc\MoneyBundle\Entity\DoctrineStorageRatio;

class DoctrineStorageRatioTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(\Tbbc\MoneyBundle\Entity\DoctrineStorageRatio::class));
    }

    public function testConstructor(): void
    {
        $dollar = new DoctrineStorageRatio('USD', 1.6);
        self::assertSame('USD', $dollar->getCurrencyCode());
        self::assertSame(1.6, $dollar->getRatio());
    }

    public function testProperties(): void
    {
        $currency = new DoctrineStorageRatio();

        self::assertNull($currency->getId());

        $currency->setCurrencyCode('USD');
        self::assertSame('USD', $currency->getCurrencyCode());

        $currency->setRatio(1.6);
        self::assertSame(1.6, $currency->getRatio());

        self::assertTrue(method_exists($currency, 'getId'));
        self::assertTrue(method_exists($currency, 'getCurrencyCode'));
        self::assertTrue(method_exists($currency, 'setCurrencyCode'));
        self::assertTrue(method_exists($currency, 'getRatio'));
        self::assertTrue(method_exists($currency, 'setRatio'));
    }
}
