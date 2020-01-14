<?php
/**
 * Created by Philippe Le Van.
 * Date: 01/07/13
 */

namespace Tbbc\MoneyBundle\Tests\Form\Type;

use Money\Currency;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tbbc\MoneyBundle\Form\Type\CurrencyType;
use Tbbc\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\Test\TypeTestCase;
use Money\Money;

class MoneyTypeTest
    extends TypeTestCase
{

    private $currencyTypeClass = 'Tbbc\MoneyBundle\Form\Type\CurrencyType';
    private $moneyTypeClass = 'Tbbc\MoneyBundle\Form\Type\MoneyType';

    public function testBindValid()
    {
        $form = $this->factory->create($this->moneyTypeClass, null, array(
            "currency_type" => $this->currencyTypeClass,
        ));
        $form->submit(array(
            "tbbc_currency" => array("tbbc_name"=>'EUR'),
            "tbbc_amount" => '12'
        ));
        $this->assertEquals(Money::EUR(1200), $form->getData());
    }

    public function testBindDecimalValid()
    {
        \Locale::setDefault("fr_FR");
        $form = $this->factory->create($this->moneyTypeClass, null, array(
            "currency_type" => $this->currencyTypeClass,
        ));
        $form->submit(array(
            "tbbc_currency" => array("tbbc_name"=>'EUR'),
            "tbbc_amount" => '12,5'
        ));
        $this->assertEquals(Money::EUR(1250), $form->getData());
    }

    public function testGreaterThan1000Valid()
    {
        \Locale::setDefault("fr_FR");
        $form = $this->factory->create($this->moneyTypeClass, null, array(
            "currency_type" => $this->currencyTypeClass,
        ));
        $form->submit(array(
            "tbbc_currency" => array("tbbc_name"=>'EUR'),
            "tbbc_amount" => '1 252,5'
        ));
        $this->assertEquals(Money::EUR(125250), $form->getData());
    }

    public function testSetData()
    {
        \Locale::setDefault("fr_FR");
        $form = $this->factory->create($this->moneyTypeClass, null, array(
            "currency_type" => $this->currencyTypeClass,
        ));
        $form->setData(Money::EUR(120));
        $formView = $form->createView();

        $this->assertEquals("1,20", $formView->children["tbbc_amount"]->vars["value"]);
    }

    public function testOptions()
    {
        \Locale::setDefault("fr_FR");
        $form = $this->factory->create($this->moneyTypeClass, null, array(
            "currency_type" => $this->currencyTypeClass,
            "amount_options" => array(
                'label' => 'Amount',
            ),
            "currency_options" => array(
                'label' => 'Currency',
            ),
        ));
        $form->setData(Money::EUR(120));
        $formView = $form->createView();

        $this->assertEquals("1,20", $formView->children["tbbc_amount"]->vars["value"]);
    }

    public function testOptionsFailsIfNotValid()
    {
        $this->expectException(UndefinedOptionsException::class);
        $this->expectExceptionMessageRegExp('/this_does_not_exists/');

        $this->factory->create($this->moneyTypeClass, null, array(
            "currency_type" => $this->currencyTypeClass,
            "amount_options" => array(
                'this_does_not_exists' => 'Amount',
            ),
            "currency_options" => array(
                'label' => 'Currency',
            ),
        ));
    }

    protected function getExtensions()
    {
        return array(
            new PreloadedExtension(
                array(new CurrencyType(array("EUR", "USD"), "EUR"), new MoneyType(2)), array()
            )
        );
    }

}
