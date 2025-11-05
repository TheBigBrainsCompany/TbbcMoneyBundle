<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Form\DataTransformer;

use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Tbbc\MoneyBundle\Form\DataTransformer\SimpleMoneyToArrayTransformer;

final class SimpleMoneyToArrayTransformerTest extends TestCase
{
    private Money $money;

    private SimpleMoneyToArrayTransformer $transformer;

    protected function setUp(): void
    {
        $currency = new Currency('EUR');
        $this->money = new Money(1000, $currency);
        $this->transformer = (new SimpleMoneyToArrayTransformer(2))
            ->setCurrency($currency->getCode());
    }

    public function testTransformValueToFormData(): void
    {
        $this->assertSame([
            'tbbc_amount' => '10.00',
        ], $this->transformer->transform($this->money));
    }

    public function testTransformNull(): void
    {
        $this->assertNull($this->transformer->transform(null));
    }

    public function testReverse(): void
    {
        $this->assertSame($this->money->getAmount(), $this->transformer->reverseTransform([
            'tbbc_amount' => '10.00',
        ])->getAmount());

        $this->assertSame($this->money->getCurrency()->getCode(), $this->transformer->reverseTransform([
            'tbbc_amount' => '10.00',
        ])->getCurrency()->getCode());
    }
}
