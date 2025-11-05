<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Form\Type;

use Money\Currency;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tbbc\MoneyBundle\Form\Type\CurrencyType;

final class CurrencyTypeTest extends TypeTestCase
{
    private array $currencies;

    protected function getExtensions(): array
    {
        $this->currencies = ['EUR', 'USD', 'GBP'];
        $reference = 'EUR';

        $type = new CurrencyType(
            $this->currencies,
            $reference
        );

        return [
            new PreloadedExtension([$type], []),
        ];
    }

    public function testView(): void
    {
        $view = $this->factory->create(CurrencyType::class)
            ->createView();

        $this->assertSame('tbbc_currency', $view->vars['id']);
        $this->assertCount(1, $view->vars['form']->children);
        $child = $view->vars['form']->children['tbbc_name'];
        $this->assertSame('tbbc_currency_tbbc_name', $child->vars['id']);
        $this->assertCount(count($this->currencies), $child->vars['choices']);
    }

    public function testSubmittedData(): void
    {
        $form = $this->factory->create(CurrencyType::class);
        $form->submit([
            'tbbc_name' => 'USD',
        ]);
        $this->assertSame((new Currency('USD'))->getCode(), $form->getData()->getCode());
    }

    public function testOptions(): void
    {
        $form = $this->factory->create(CurrencyType::class, null, [
            'currency_options' => [
                'label' => 'currency label',
            ],
        ]);

        $form->setData(new Currency('USD'));
        $formView = $form->createView();

        $this->assertSame('USD', $formView->children['tbbc_name']->vars['value']);
    }

    public function testOptionsFailsIfNotValid(): void
    {
        $this->expectException(UndefinedOptionsException::class);
        $this->expectExceptionMessageMatches('/this_does_not_exists/');

        $this->factory->create(CurrencyType::class, null, [
            'currency_options' => [
                'this_does_not_exists' => 'currency label',
            ],
        ]);
    }
}
