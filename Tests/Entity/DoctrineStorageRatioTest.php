<?php
namespace Tbbc\MoneyBundle\Tests\Entity;

use Tbbc\MoneyBundle\Entity\DoctrineStorageRatio;

/**
 * @group manager
 */
class DoctrineStorageRatioTest extends \PHPUnit_Framework_TestCase
{
    public function testClassExists ()
    {
        $this->assertTrue(class_exists('\Tbbc\MoneyBundle\Entity\DoctrineStorageRatio'));
    }

    public function testConstructor ()
    {
        $eurToUsd = new DoctrineStorageRatio('EUR/USD', 1.6);
        
        $this->assertEquals('EUR/USD', $eurToUsd->getCurrencyCodePair());
        $this->assertEquals(1.6, $eurToUsd->getRatio());
    }

    public function testProperties ()
    {
        $currency = new DoctrineStorageRatio();

        $this->assertTrue(method_exists($currency, 'getId'));
        $this->assertTrue(method_exists($currency, 'getCurrencyCodePair'));
        $this->assertTrue(method_exists($currency, 'setCurrencyCodePair'));
        $this->assertTrue(method_exists($currency, 'getRatio'));
        $this->assertTrue(method_exists($currency, 'setRatio'));
    }
}
