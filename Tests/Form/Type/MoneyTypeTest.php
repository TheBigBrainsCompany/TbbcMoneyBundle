<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Form\Type;

use Locale;
use Money\Money;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tbbc\MoneyBundle\Form\Type\CurrencyType;
use Tbbc\MoneyBundle\Form\Type\MoneyType;

final class MoneyTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $type = new MoneyType(2);
        $currency = new CurrencyType(['USD', 'EUR'], 'EUR');

        return [
            new PreloadedExtension([$type, $currency], []),
        ];
    }

    public function testView(): void
    {
        $view = $this->factory->create(MoneyType::class)
            ->createView();

        $this->assertSame('tbbc_money', $view->vars['id']);
        $this->assertCount(2, $view->vars['form']->children);
        $child = $view->vars['form']->children['tbbc_currency'];
        $this->assertSame('tbbc_money_tbbc_currency', $child->vars['id']);

        $child = $view->vars['form']->children['tbbc_amount'];
        $this->assertSame('tbbc_money_tbbc_amount', $child->vars['id']);
    }

    public function testBindValid(): void
    {
        $form = $this->factory->create(MoneyType::class, null, [
            'currency_type' => CurrencyType::class,
        ]);
        $form->submit([
            'tbbc_currency' => [
                'tbbc_name' => 'EUR',
            ],
            'tbbc_amount' => '12',
        ]);
        $money = Money::EUR(1200);
        $this->assertSame($money->getAmount(), $form->getData()->getAmount());
        $this->assertSame($money->getCurrency()->getCode(), $form->getData()->getCurrency()->getCode());
    }

    public function testBindDecimalValid(): void
    {
        Locale::setDefault('fr_FR');
        $form = $this->factory->create(MoneyType::class, null, [
            'currency_type' => CurrencyType::class,
        ]);
        $form->submit([
            'tbbc_currency' => [
                'tbbc_name' => 'EUR',
            ],
            'tbbc_amount' => '12,5',
        ]);
        $money = Money::EUR(1250);
        $this->assertSame($money->getAmount(), $form->getData()->getAmount());
        $this->assertSame($money->getCurrency()->getCode(), $form->getData()->getCurrency()->getCode());
    }

    public function testGreaterThan1000Valid(): void
    {
        Locale::setDefault('fr_FR');
        $form = $this->factory->create(MoneyType::class, null, [
            'currency_type' => CurrencyType::class,
        ]);
        $form->submit([
            'tbbc_currency' => [
                'tbbc_name' => 'EUR',
            ],
            'tbbc_amount' => '1 252,5',
        ]);
        $money = Money::EUR(125250);
        $this->assertSame($money->getAmount(), $form->getData()->getAmount());
        $this->assertSame($money->getCurrency()->getCode(), $form->getData()->getCurrency()->getCode());
    }

    public function testSetData(): void
    {
        Locale::setDefault('fr_FR');
        $form = $this->factory->create(MoneyType::class, null, [
            'currency_type' => CurrencyType::class,
        ]);
        $form->setData(Money::EUR(120));
        $formView = $form->createView();

        $this->assertSame('1,20', $formView->children['tbbc_amount']->vars['value']);
    }

    public function testOptions(): void
    {
        Locale::setDefault('fr_FR');
        $form = $this->factory->create(MoneyType::class, null, [
            'currency_type' => CurrencyType::class,
            'amount_options' => [
                'label' => 'Amount',
            ],
            'currency_options' => [
                'label' => 'Currency',
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

        $this->factory->create(MoneyType::class, null, [
            'currency_type' => CurrencyType::class,
            'amount_options' => [
                'this_does_not_exists' => 'Amount',
            ],
            'currency_options' => [
                'label' => 'Currency',
            ],
        ]);
    }
}
