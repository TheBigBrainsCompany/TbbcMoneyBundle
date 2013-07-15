<?php
namespace Tbbc\MoneyBundle\Tests\Entity;

use Tbbc\MoneyBundle\Entity\Currency;

/**
 * @group manager
 */
class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    public function testClassExists ()
    {
        $this->assertTrue(class_exists('\Tbbc\MoneyBundle\Entity\Currency'));
    }

    public function testConstructor ()
    {
        $dollar = new Currency('USD', 1.6);
        
        $this->assertEquals('USD', $dollar->getCurrencyCode());
        $this->assertEquals(1.6, $dollar->getRatio());
    }

    public function testProperties ()
    {
        $currency = new Currency();

        $this->assertTrue(method_exists($currency, 'getId'));
        $this->assertTrue(method_exists($currency, 'getCurrencyCode'));
        $this->assertTrue(method_exists($currency, 'setCurrencyCode'));
        $this->assertTrue(method_exists($currency, 'getRatio'));
        $this->assertTrue(method_exists($currency, 'setRatio'));
    }
}
