<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Form\DataTransformer;

use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Tbbc\MoneyBundle\Form\DataTransformer\MoneyToArrayTransformer;

class MoneyToArrayTransformerTest extends TestCase
{
    public function testTransformMoneyToArray(): void
    {
        $currency = new Currency('EUR');
        $value = new Money('100', $currency);
        $transformer = new MoneyToArrayTransformer();
        self::assertSame(
            [
                'tbbc_amount' => '1.00',
                'tbbc_currency' => $currency,
            ],
            $transformer->transform($value)
        );
    }

    public function testTransformNull(): void
    {
        $transformer = new MoneyToArrayTransformer();
        self::assertNull($transformer->transform(null));
    }

    public function testTransformThrowErrorIfValueIsNotCurrency(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $transformer = new MoneyToArrayTransformer();
        $transformer->transform('EUR');
    }

    public function testReverseValueToCurrency(): void
    {
        $value = ['tbbc_amount' => '1.00', 'tbbc_currency' => 'EUR'];
        $expected = new Money('100', new Currency('EUR'));
        $transformer = new MoneyToArrayTransformer();
        $transformed = $transformer->reverseTransform($value);
        self::assertSame(
            $expected->getAmount(),
            $transformed->getAmount()
        );
        self::assertSame(
            $expected->getCurrency()->getCode(),
            $transformed->getCurrency()->getCode()
        );
    }

    public function testReverseToNullIfValueIsNull(): void
    {
        $value = null;
        $transformer = new MoneyToArrayTransformer();
        self::assertNull($transformer->reverseTransform($value));
    }

    public function testReverseToNullIfFormElementNotSet(): void
    {
        $value = ['tbbc_name' => null];
        $transformer = new MoneyToArrayTransformer();
        self::assertNull($transformer->reverseTransform($value));
    }

    public function testReverseFormValueIsNotArray(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $value = 'EUR';
        $transformer = new MoneyToArrayTransformer();
        $transformer->reverseTransform($value);
    }
}
