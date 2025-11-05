<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Entity;

use DateTime;
use PHPUnit\Framework\TestCase;
use Tbbc\MoneyBundle\Entity\RatioHistory;

final class RatioHistoryTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(RatioHistory::class));
    }

    public function testProperties(): void
    {
        $ratioHistory = new RatioHistory();

        $this->assertNull($ratioHistory->getId());
        $ratioHistory->setId(1);
        $this->assertSame(1, $ratioHistory->getId());

        $ratioHistory->setCurrencyCode('USD');
        $this->assertSame('USD', $ratioHistory->getCurrencyCode());

        $ratioHistory->setRatio(1.6);
        $this->assertEqualsWithDelta(1.6, $ratioHistory->getRatio(), PHP_FLOAT_EPSILON);

        $ratioHistory->setReferenceCurrencyCode('code');
        $this->assertSame('code', $ratioHistory->getReferenceCurrencyCode());

        $ratioHistory->setSavedAt(new DateTime('2012-01-01'));
        $this->assertSame('2012-01-01', $ratioHistory->getSavedAt()->format('Y-m-d'));

        $this->assertTrue(method_exists($ratioHistory, 'getId'));
        $this->assertTrue(method_exists($ratioHistory, 'getCurrencyCode'));
        $this->assertTrue(method_exists($ratioHistory, 'setCurrencyCode'));
        $this->assertTrue(method_exists($ratioHistory, 'getRatio'));
        $this->assertTrue(method_exists($ratioHistory, 'setRatio'));
        $this->assertTrue(method_exists($ratioHistory, 'setReferenceCurrencyCode'));
        $this->assertTrue(method_exists($ratioHistory, 'getReferenceCurrencyCode'));
        $this->assertTrue(method_exists($ratioHistory, 'setSavedAt'));
        $this->assertTrue(method_exists($ratioHistory, 'getSavedAt'));
    }
}
