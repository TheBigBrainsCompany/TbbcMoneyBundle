<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Form\DataTransformer;

use Money\Currency;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Tbbc\MoneyBundle\Form\DataTransformer\CurrencyToArrayTransformer;

class CurrencyToArrayTransformerTest extends TestCase
{
    public function testTransformCurrencyToArray(): void
    {
        $value = new Currency('EUR');
        $transformer = new CurrencyToArrayTransformer();
        self::assertSame(
            ['tbbc_name' => 'EUR'],
            $transformer->transform($value)
        );
    }

    public function testTransformNull(): void
    {
        $transformer = new CurrencyToArrayTransformer();
        self::assertNull($transformer->transform(null));
    }

    public function testTransformThrowErrorIfValueIsNotCurrency(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $transformer = new CurrencyToArrayTransformer();
        $transformer->transform('EUR');
    }

    public function testReverseValueToCurrency(): void
    {
        $value = ['tbbc_name' => 'EUR'];
        $expected = new Currency('EUR');
        $transformer = new CurrencyToArrayTransformer();
        self::assertSame(
            $expected->getCode(),
            $transformer->reverseTransform($value)->getCode()
        );
    }

    public function testReverseToNullIfValueIsNull(): void
    {
        $value = null;
        $transformer = new CurrencyToArrayTransformer();
        self::assertNull($transformer->reverseTransform($value));
    }

    public function testReverseToNullIfFormElementNotSet(): void
    {
        $value = ['tbbc_name' => null];
        $transformer = new CurrencyToArrayTransformer();
        self::assertNull($transformer->reverseTransform($value));
    }

    public function testReverseFormValueIsNotArray(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $value = 'EUR';
        $transformer = new CurrencyToArrayTransformer();
        $transformer->reverseTransform($value);
    }

    public function testReverseThrowExceptionIfCurrencyCodeNotValid(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $value = ['tbbc_name' => 123];
        $transformer = new CurrencyToArrayTransformer();
        $transformer->reverseTransform($value);
    }
}
