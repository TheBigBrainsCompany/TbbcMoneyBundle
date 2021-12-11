<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Templating\Helper;

use Money\Currency;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Tbbc\MoneyBundle\Templating\Helper\CurrencyHelper;

class CurrencyHelperTest extends TestCase
{
    private MoneyFormatter|MockObject $formatter;
    private CurrencyHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formatter = $this->createMock(MoneyFormatter::class);
        $this->helper = new CurrencyHelper($this->formatter);
    }

    public function testGetName(): void
    {
        $this->assertSame('tbbc_money_currency_helper', $this->helper->getName());
    }

    public function testName(): void
    {
        $currency = new Currency('USD');
        $this->formatter
            ->expects($this->once())
            ->method('formatCurrencyAsName')
            ->willReturn($currency->getCode());

        self::assertSame($currency->getCode(), $this->helper->name($currency));
    }

    public function testSymbol(): void
    {
        $currency = new Currency('USD');
        $this->formatter
            ->expects($this->once())
            ->method('formatCurrencyAsSymbol')
            ->willReturn('$');

        self::assertSame('$', $this->helper->symbol($currency));
    }
}
