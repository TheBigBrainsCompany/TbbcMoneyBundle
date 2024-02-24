<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Formatter;

use Locale;
use Money\Currency;
use Money\Money;
use NumberFormatter;
use PHPUnit\Framework\TestCase;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;

class MoneyFormatterTest extends TestCase
{
    protected MoneyFormatter $formatter;

    protected Money $inputMoney;

    public function setUp(): void
    {
        Locale::setDefault('fr_FR');
        $this->formatter = new MoneyFormatter(2);
        $this->inputMoney = new Money(123_456_789, new Currency('EUR'));
    }

    public function testLocalizedFormatMoney(): void
    {
        // check locale
        Locale::setDefault('fr_FR');
        $this->assertSame("1\u{202f}234\u{202f}567,89\u{a0}€", $this->formatter->localizedFormatMoney($this->inputMoney));
        Locale::setDefault('en_US');
        $this->assertSame('€1,234,567.89', $this->formatter->localizedFormatMoney($this->inputMoney));
        $this->assertSame("1\u{202f}234\u{202f}567,89\u{a0}€", $this->formatter->localizedFormatMoney($this->inputMoney, 'fr'));

        // check new currency
        $money = new Money(123_456_789, new Currency('USD'));
        $this->assertSame("1\u{202f}234\u{202f}567,89\u{a0}\$US", $this->formatter->localizedFormatMoney($money, 'fr'));
        $this->assertSame('$1,234,567.89', $this->formatter->localizedFormatMoney($money, 'en'));

        // ckeck decimals
        $formatter = new MoneyFormatter(4);
        Locale::setDefault('fr_FR');
        $this->assertSame("12\u{202f}345,6789\u{a0}€", $formatter->localizedFormatMoney($this->inputMoney));

        // check with custom formatter
        $numberFormatter = new NumberFormatter('fr', NumberFormatter::CURRENCY);
        $numberFormatter->setTextAttribute(NumberFormatter::CURRENCY_CODE, 'EUR');
        $numberFormatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 3);
        $this->assertSame("12\u{202f}345,679\u{a0}€", $formatter->localizedFormatMoney($this->inputMoney, null, $numberFormatter));
    }

    public function testFormatMoneyWithDefaultSeparators(): void
    {
        $value = $this->formatter->formatMoney($this->inputMoney);
        $this->assertSame('1 234 567,89 €', $value);
    }

    public function testFormatMoneyWithDefaultSeparatorsAndDecimals3(): void
    {
        $this->formatter = new MoneyFormatter(4);
        $value = $this->formatter->formatMoney($this->inputMoney);
        $this->assertSame('12 345,6789 €', $value);
    }

    public function testFormatMoneyWithCustomSeparators(): void
    {
        $value = $this->formatter->formatMoney($this->inputMoney, '.', ',');
        $this->assertSame('1,234,567.89 €', $value);
    }

    public function testFormatAmountWithDefaultSeparators(): void
    {
        $value = $this->formatter->formatAmount($this->inputMoney);
        $this->assertSame('1 234 567,89', $value);
    }

    public function testFormatAmountWithCustomSeparators(): void
    {
        $value = $this->formatter->formatAmount($this->inputMoney, '.', ',');
        $this->assertSame('1,234,567.89', $value);
    }

    public function testAsFloatIsReturningAFloat(): void
    {
        $value = $this->formatter->asFloat($this->inputMoney);
        $this->assertIsFloat($value);
    }

    public function testFormatCurrency(): void
    {
        $value = $this->formatter->formatCurrency($this->inputMoney);
        $this->assertSame('€', $value);
    }

    public function testFormatCurrencyAsSymbol(): void
    {
        $value = $this->formatter->formatCurrencyAsSymbol($this->inputMoney->getCurrency());
        $this->assertSame('€', $value);
    }

    public function testFormatCurrencyAsName(): void
    {
        $value = $this->formatter->formatCurrencyAsName($this->inputMoney->getCurrency());
        $this->assertSame('EUR', $value);
    }

    public function testGetCurrency(): void
    {
        $value = $this->formatter->getCurrency($this->inputMoney);
        $this->assertInstanceOf(\Money\Currency::class, $value);
        $currency = new Currency('EUR');
        $this->assertSame($currency->getCode(), $value->getCode());
    }
}
