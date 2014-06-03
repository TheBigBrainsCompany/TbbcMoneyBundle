<?php

namespace Tbbc\MoneyBundle\Tests\Formatter;

use Money\Currency;
use Money\Money;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;

/**
 * Class MoneyFormatterTest
 * @package Tbbc\MoneyBundle\Tests\Formatter
 * @group formatter
 */
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
        $this->formatter = new MoneyFormatter(2);
        $this->inputMoney = new Money(123456789, new Currency('EUR'));
    }

    public function testLocalizedFormatMoney()
    {
        // check locale
        \Locale::setDefault('fr_FR');
        $this->assertEquals('1 234 567,89 €', $this->formatter->localizedFormatMoney($this->inputMoney));
        \Locale::setDefault('en_US');
        $this->assertEquals('€1,234,567.89', $this->formatter->localizedFormatMoney($this->inputMoney));
        $this->assertEquals('1 234 567,89 €', $this->formatter->localizedFormatMoney($this->inputMoney, 'fr'));

        // check new currency
        $money = new Money(123456789, new Currency('USD'));
        $this->assertEquals('1 234 567,89 $US', $this->formatter->localizedFormatMoney($money, 'fr'));
        $this->assertEquals('$1,234,567.89', $this->formatter->localizedFormatMoney($money, 'en'));

        // ckeck decimals
        $formatter = new MoneyFormatter(4);
        \Locale::setDefault('fr_FR');
        $this->assertEquals('12 345,6789 €', $formatter->localizedFormatMoney($this->inputMoney));

        // check with custom formatter
        $numberFormatter = new \NumberFormatter('fr', \NumberFormatter::CURRENCY);
        $numberFormatter->setTextAttribute(\NumberFormatter::CURRENCY_CODE, 'EUR');
        $numberFormatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 3);
        $this->assertEquals('12 345,679 €', $formatter->localizedFormatMoney($this->inputMoney, null, $numberFormatter));

    }

    public function testFormatMoneyWithDefaultSeparators()
    {
        $value = $this->formatter->formatMoney($this->inputMoney);
        $this->assertEquals('1 234 567,89 €', $value);
    }
    public function testFormatMoneyWithDefaultSeparatorsAndDecimals3()
    {
        $this->formatter = new MoneyFormatter(4);
        $value = $this->formatter->formatMoney($this->inputMoney);
        $this->assertEquals('12 345,6789 €', $value);
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
