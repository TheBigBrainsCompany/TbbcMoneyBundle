<?php

namespace Tbbc\MoneyBundle\Tests\Formatter;

use Money\Currency;
use Money\Money;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;

class MoneyFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MoneyFormatter
     */
    protected $formatter;

    /**
     * @var Money
     */
    protected $inputMoney;

    public function setUp()
    {
        \Locale::setDefault('fr_FR');
        $this->formatter = new MoneyFormatter();
        $this->inputMoney = new Money(123456789, new Currency('EUR'));
    }

    public function testFormatMoneyWithDefaultSeparators()
    {
        $value = $this->formatter->formatMoney($this->inputMoney);
        $this->assertEquals('1 234 567,89 €', $value);
    }

    public function testFormatMoneyWithCustomSeparators()
    {
        $value = $this->formatter->formatMoney($this->inputMoney, '.', ',');
        $this->assertEquals('1,234,567.89 €', $value);
    }

    public function testFormatAmountWithDefaultSeparators()
    {
        $value = $this->formatter->formatAmount($this->inputMoney);
        $this->assertEquals('1 234 567,89', $value);
    }

    public function testFormatAmountWithCustomSeparators()
    {
        $value = $this->formatter->formatAmount($this->inputMoney, '.', ',');
        $this->assertEquals('1,234,567.89', $value);
    }

    public function testAsFloatIsReturningAFloat()
    {
        $value = $this->formatter->asFloat($this->inputMoney);
        $this->assertTrue(is_float($value));
    }

    public function testFormatCurrency()
    {
        $value = $this->formatter->formatCurrency($this->inputMoney);
        $this->assertEquals('€', $value);
    }

    public function testFormatCurrencyAsSymbol()
    {
        $value = $this->formatter->formatCurrencyAsSymbol($this->inputMoney->getCurrency());
        $this->assertEquals('€', $value);
    }

    public function testFormatCurrencyAsName()
    {
        $value = $this->formatter->formatCurrencyAsName($this->inputMoney->getCurrency());
        $this->assertEquals('EUR', $value);
    }

    public function testGetCurrency()
    {
        $value = $this->formatter->getCurrency($this->inputMoney);
        $this->assertInstanceOf('Money\Currency', $value);
        $this->assertEquals(new Currency('EUR'), $value);
    }
}
