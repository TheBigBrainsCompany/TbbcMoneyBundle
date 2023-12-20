<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Entity;

use DateTime;
use PHPUnit\Framework\TestCase;
use Tbbc\MoneyBundle\Entity\RatioHistory;

class RatioHistoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(\Tbbc\MoneyBundle\Entity\RatioHistory::class));
    }

    public function testProperties(): void
    {
        $ratioHistory = new RatioHistory();

        self::assertNull($ratioHistory->getId());
        $ratioHistory->setId(1);
        self::assertSame(1, $ratioHistory->getId());

        $ratioHistory->setCurrencyCode('USD');
        self::assertSame('USD', $ratioHistory->getCurrencyCode());

        $ratioHistory->setRatio(1.6);
        self::assertSame(1.6, $ratioHistory->getRatio());

        $ratioHistory->setReferenceCurrencyCode('code');
        self::assertSame('code', $ratioHistory->getReferenceCurrencyCode());

        $ratioHistory->setSavedAt(new DateTime('2012-01-01'));
        self::assertSame('2012-01-01', $ratioHistory->getSavedAt()->format('Y-m-d'));

        self::assertTrue(method_exists($ratioHistory, 'getId'));
        self::assertTrue(method_exists($ratioHistory, 'getCurrencyCode'));
        self::assertTrue(method_exists($ratioHistory, 'setCurrencyCode'));
        self::assertTrue(method_exists($ratioHistory, 'getRatio'));
        self::assertTrue(method_exists($ratioHistory, 'setRatio'));
        self::assertTrue(method_exists($ratioHistory, 'setReferenceCurrencyCode'));
        self::assertTrue(method_exists($ratioHistory, 'getReferenceCurrencyCode'));
        self::assertTrue(method_exists($ratioHistory, 'setSavedAt'));
        self::assertTrue(method_exists($ratioHistory, 'getSavedAt'));
    }
}
