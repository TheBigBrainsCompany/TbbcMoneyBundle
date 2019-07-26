<?php
/**
 * Created by Philippe Le Van.
 * Date: 01/07/13
 */

namespace Tbbc\MoneyBundle\Tests\Form\Type;

use Money\Currency;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tbbc\MoneyBundle\Form\Type\CurrencyType;

class CurrencyTypeTest
    extends TypeTestCase
{
    private $currencyTypeClass = 'Tbbc\MoneyBundle\Form\Type\CurrencyType';

    public function testBindValid()
    {
        $form = $this->factory->create($this->currencyTypeClass, null, array());
        $form->submit(array("tbbc_name" => "EUR"));
        $this->assertEquals(new Currency('EUR'), $form->getData());
    }

    public function testSetData()
    {
        \Locale::setDefault("fr_FR");
        $form = $this->factory->create($this->currencyTypeClass, null, array());
        $form->setData(new Currency("USD"));
        $formView = $form->createView();

        $this->assertEquals("USD", $formView->children["tbbc_name"]->vars["value"]);
    }

    public function testOptions()
    {
        $form = $this->factory->create($this->currencyTypeClass, null, array(
            'currency_options' => array(
                'label' => 'currency label',
            )
        ));

        $form->setData(new Currency("USD"));
        $formView = $form->createView();

        $this->assertEquals("USD", $formView->children["tbbc_name"]->vars["value"]);
    }

    public function testOptionsFailsIfNotValid()
    {
        $this->expectException(UndefinedOptionsException::class);
        $this->expectExceptionMessageRegExp('/this_does_not_exists/');

        $this->factory->create($this->currencyTypeClass, null, array(
            'currency_options' => array(
                'this_does_not_exists' => 'currency label',
            )
        ));
    }

    protected function getExtensions()
    {
        return array(
            new PreloadedExtension(
                array(new CurrencyType(array("EUR", "USD"), "EUR")), array()
            )
        );
    }

}
