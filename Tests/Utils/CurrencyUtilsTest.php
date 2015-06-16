<?php
/**
 * Created by IntelliJ IDEA.
 * User: sowhat
 */

namespace Tbbc\MoneyBundle\Utils;


use Money\Currency;

class CurrencyUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCurrency()
    {
        $this->assertFalse(CurrencyUtils::isCurrency('EUR'));
        $this->assertFalse(CurrencyUtils::isCurrency(array()));
        $this->assertFalse(CurrencyUtils::isCurrency(new \stdClass()));
        $this->assertTrue(CurrencyUtils::isCurrency(new Currency('EUR')));
    }

    public function testIsInCurrencyCodeFormat()
    {
        $this->assertTrue(CurrencyUtils::isInCurrencyCodeFormat('EUR'));
        $this->assertFalse(CurrencyUtils::isInCurrencyCodeFormat('someWrongString'));
        $this->assertFalse(CurrencyUtils::isInCurrencyCodeFormat(array()));
        $this->assertFalse(CurrencyUtils::isInCurrencyCodeFormat(new \stdClass()));
    }
}
