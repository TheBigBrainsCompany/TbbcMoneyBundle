<?php
namespace Tbbc\MoneyBundle\Tests\Entity;

use Tbbc\MoneyBundle\Entity\DoctrineStorageRatio;
use PHPUnit\Framework\TestCase;

/**
 * @group manager
 */
class DoctrineStorageRatioTest extends TestCase
{
    public function testClassExists ()
    {
        $this->assertTrue(class_exists('\Tbbc\MoneyBundle\Entity\DoctrineStorageRatio'));
    }

    public function testConstructor ()
    {
        $dollar = new DoctrineStorageRatio('USD', 1.6);

        $this->assertEquals('USD', $dollar->getCurrencyCode());
        $this->assertEquals(1.6, $dollar->getRatio());
    }

    public function testProperties ()
    {
        $currency = new DoctrineStorageRatio();

        $this->assertTrue(method_exists($currency, 'getId'));
        $this->assertTrue(method_exists($currency, 'getCurrencyCode'));
        $this->assertTrue(method_exists($currency, 'setCurrencyCode'));
        $this->assertTrue(method_exists($currency, 'getRatio'));
        $this->assertTrue(method_exists($currency, 'setRatio'));
    }
}
