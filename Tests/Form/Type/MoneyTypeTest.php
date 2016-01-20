<?php
/**
 * Created by Philippe Le Van.
 * Date: 01/07/13
 */

namespace Tbbc\MoneyBundle\Tests\Form\Type;

use Money\Currency;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Tbbc\MoneyBundle\Form\Type\CurrencyType;
use Tbbc\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\Test\TypeTestCase;
use Money\Money;

class MoneyTypeTest
    extends TypeTestCase
{

    private $currencyTypeClass;
    private $moneyTypeClass;

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

    protected function getExtensions()
    {
        $currencyType = new CurrencyType(array("EUR", "USD"), "EUR");
        $moneyType =  new MoneyType(2);
        $this->currencyTypeClass = get_class($currencyType);
        $this->moneyTypeClass = get_class($moneyType);
        return array(
            new PreloadedExtension(
                array($currencyType,$moneyType), array()
            )
        );
    }

}