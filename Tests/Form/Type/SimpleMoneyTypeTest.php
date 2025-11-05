<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Form\Type;

use Locale;
use Money\Money;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tbbc\MoneyBundle\Form\Type\SimpleMoneyType;
use Tbbc\MoneyBundle\Pair\PairManager;

class SimpleMoneyTypeTest extends TypeTestCase
{
    private string $simpleMoneyTypeClass = SimpleMoneyType::class;

    public function testBindValid(): void
    {
        $form = $this->factory->create($this->simpleMoneyTypeClass, null, []);
        $form->submit([
            'tbbc_amount' => '12',
        ]);
        $money = Money::EUR(1200);
        $this->assertSame($money->getAmount(), $form->getData()->getAmount());
        $this->assertSame($money->getCurrency()->getCode(), $form->getData()->getCurrency()->getCode());
    }

    public function testBindValidDecimals(): void
    {
        Locale::setDefault('fr_FR');
        $form = $this->factory->create($this->simpleMoneyTypeClass, null, []);
        $form->submit([
            'tbbc_amount' => '1,2',
        ]);
        $money = Money::EUR(1200);
        $this->assertSame($money->getAmount(), $form->getData()->getAmount());
        $this->assertSame($money->getCurrency()->getCode(), $form->getData()->getCurrency()->getCode());
    }

    public function testBindDecimalValid(): void
    {
        Locale::setDefault('fr_FR');
        $form = $this->factory->create($this->simpleMoneyTypeClass, null, []);
        $form->submit([
            'tbbc_amount' => '12,5',
        ]);
        $money = Money::EUR(1250);
        $this->assertSame($money->getAmount(), $form->getData()->getAmount());
        $this->assertSame($money->getCurrency()->getCode(), $form->getData()->getCurrency()->getCode());
    }

    public function testGreaterThan1000Valid(): void
    {
        Locale::setDefault('fr_FR');
        $form = $this->factory->create($this->simpleMoneyTypeClass, null, []);
        $form->submit([
            'tbbc_amount' => '1 252,5',
        ]);
        $money = Money::EUR(125250);
        $this->assertSame($money->getAmount(), $form->getData()->getAmount());
        $this->assertSame($money->getCurrency()->getCode(), $form->getData()->getCurrency()->getCode());
    }

    public function testSetData(): void
    {
        Locale::setDefault('fr_FR');
        $form = $this->factory->create($this->simpleMoneyTypeClass, null, []);
        $form->setData(Money::EUR(120));
        $formView = $form->createView();

        $this->assertSame('1,20', $formView->children['tbbc_amount']->vars['value']);
    }

    public function testOptions(): void
    {
        Locale::setDefault('fr_FR');
        $form = $this->factory->create($this->simpleMoneyTypeClass, null, [
            'amount_options' => [
                'label' => 'Amount',
            ],
        ]);
        $form->setData(Money::EUR(120));
        $formView = $form->createView();

        $this->assertSame('1,20', $formView->children['tbbc_amount']->vars['value']);
    }

    public function testOptionsFailsIfNotValid(): void
    {
        $this->expectException(UndefinedOptionsException::class);
        $this->expectExceptionMessageMatches('/this_does_not_exists/');

        $this->factory->create($this->simpleMoneyTypeClass, null, [
            'amount_options' => [
                'this_does_not_exists' => 'Amount',
            ],
        ]);
    }

    protected function getExtensions(): array
    {
        //This is probably not ideal, but I'm not sure how to set up the pair manager
        // with different decimals for different tests in Symfony 3.0
        $decimals = 2;
        $currencies = ['EUR', 'USD'];
        $referenceCurrency = 'EUR';

        # PHPUnit 10
        if (method_exists($this, 'name') && 'testBindValidDecimals' === $this->name()) {
            $decimals = 3;
        }

        # PHPUnit 9
        if (method_exists($this, 'getName') && 'testBindValidDecimals' === $this->getName()) {
            $decimals = 3;
        }

        $pairManager = $this->getMockBuilder(PairManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pairManager->expects($this->any())
            ->method('getReferenceCurrencyCode')
            ->willReturn('EUR');

        return [
            new PreloadedExtension(
                [new SimpleMoneyType($decimals, $currencies, $referenceCurrency)], []
            ),
        ];
    }

    public function testOverrideCurrency(): void
    {
        Locale::setDefault('fr_FR');
        $form = $this->factory->create($this->simpleMoneyTypeClass, null, ['currency' => 'USD']);
        $form->submit([
            'tbbc_amount' => '1 252,5',
        ]);
        $money = Money::USD(125250);
        $this->assertSame($money->getAmount(), $form->getData()->getAmount());
        $this->assertSame($money->getCurrency()->getCode(), $form->getData()->getCurrency()->getCode());
    }
}
