<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tbbc\MoneyBundle\Type\MoneyType;

class MoneyTypeTest extends TestCase
{
    private MoneyType $type;
    private AbstractPlatform|MockObject $platform;

    protected function setUp(): void
    {
        $this->type = new MoneyType();
        $this->platform = $this->createMock(AbstractPlatform::class);
    }

    public function testGetSqlDeclaration(): void
    {
        $this->platform
            ->expects($this->once())
            ->method('getStringTypeDeclarationSQL')
            ->with(['varchar'])
            ->willReturn('varchar(255)');
        self::assertSame('varchar(255)', $this->type->getSqlDeclaration(['varchar'], $this->platform));
    }

    public function testRequiresSQLCommentHint(): void
    {
        self::assertTrue($this->type->requiresSQLCommentHint($this->platform));
    }

    public function testConvertToPHPValueNullValue(): void
    {
        self::assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testConvertToPHPValueValue(): void
    {
        $money = new Money(100, new Currency('EUR'));
        $value = $money->getCurrency()->getCode().' '.$money->getAmount();
        self::assertSame(
            $money->getAmount(),
            $this->type->convertToPHPValue($value, $this->platform)->getAmount()
        );

        self::assertSame(
            $money->getCurrency()->getCode(),
            $this->type->convertToPHPValue($value, $this->platform)->getCurrency()->getCode()
        );
    }

    public function testConvertToDatabaseValueNull(): void
    {
        self::assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testConvertToDatabaseValueMoney(): void
    {
        $money = new Money(100, new Currency('EUR'));
        $value = $money->getCurrency()->getCode().' '.$money->getAmount();
        self::assertSame($value, $this->type->convertToDatabaseValue($money, $this->platform));
    }

    public function testConvertToDatabaseValueUnknown(): void
    {
        $this->expectException(ConversionException::class);
        $this->type->convertToDatabaseValue('foobar', $this->platform);
    }

    public function testName(): void
    {
        self::assertSame('money', $this->type->getName());
    }
}
