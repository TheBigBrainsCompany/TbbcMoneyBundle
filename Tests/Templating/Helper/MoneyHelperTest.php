<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Templating\Helper;

use Money\Currency;
use Money\Money;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;
use Tbbc\MoneyBundle\Templating\Helper\MoneyHelper;

class MoneyHelperTest extends TestCase
{
    private MoneyFormatter|MockObject $moneyFormatter;
    private PairManagerInterface|MockObject $pairManager;
    private MoneyHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->moneyFormatter = $this->createMock(MoneyFormatter::class);
        $this->pairManager = $this->createMock(PairManagerInterface::class);
        $this->helper = new MoneyHelper($this->moneyFormatter, $this->pairManager);
    }

    public function testFormat(): void
    {
        $money = new Money(100000, new Currency('EUR'));
        $this->moneyFormatter
            ->expects($this->once())
            ->method('formatMoney')
            ->with($money, '.', '_')
            ->willReturn('1_000.00 EUR');
        self::assertSame('1_000.00 EUR', $this->helper->format($money, '.', '_'));
    }

    public function testFormatAmount(): void
    {
        $money = new Money(100000, new Currency('EUR'));
        $this->moneyFormatter
            ->expects($this->once())
            ->method('formatAmount')
            ->with($money, '.', '_')
            ->willReturn('1_000.00 EUR');
        self::assertSame('1_000.00 EUR', $this->helper->formatAmount($money, '.', '_'));
    }

    public function testAsFloat(): void
    {
        $money = new Money(100000, new Currency('EUR'));
        $this->moneyFormatter
            ->expects($this->once())
            ->method('asFloat')
            ->with($money)
            ->willReturn(1000.00);
        self::assertSame(1000.00, $this->helper->asFloat($money));
    }

    public function testFormatCurrency(): void
    {
        $money = new Money(100000, new Currency('EUR'));
        $this->moneyFormatter
            ->expects($this->once())
            ->method('formatCurrency')
            ->with($money)
            ->willReturn('$');
        self::assertSame('$', $this->helper->formatCurrency($money));
    }

    public function testGetCurrency(): void
    {
        $money = new Money(100000, new Currency('EUR'));
        $this->moneyFormatter
            ->expects($this->once())
            ->method('getCurrency')
            ->with($money)
            ->willReturn($money->getCurrency());
        self::assertSame($money->getCurrency(), $this->helper->getCurrency($money));
    }

    public function testConvert(): void
    {
        $money = new Money(100000, new Currency('EUR'));
        $returnMoney = new Money(5000, new Currency('USD'));
        $currency = 'USD';
        $this->pairManager
            ->expects($this->once())
            ->method('convert')
            ->with($money, $currency)
            ->willReturn($returnMoney);
        self::assertSame($returnMoney, $this->helper->convert($money, $currency));
    }

    public function testName(): void
    {
        self::assertSame('tbbc_money_helper', $this->helper->getName());
    }
}
