<?php
/**
 * Created by Philippe Le Van.
 * Date: 01/07/13
 */

namespace Tbbc\MoneyBundle\Tests\Form\Type;

use Money\Currency;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Tbbc\MoneyBundle\Form\Type\CurrencyType;
use Symfony\Component\Form\Test\TypeTestCase;

class CurrencyTypeTest
    extends TypeTestCase
{
    public function testBindValid()
    {
        $currencyType = new CurrencyType(
            array("EUR", "USD"),
            "EUR"
        );
        $form = $this->factory->create($currencyType, null, array());
        $form->bind(array("tbbc_name" => "EUR"));
        $this->assertEquals(new Currency('EUR'), $form->getData());
    }

    public function testSetData()
    {
        \Locale::setDefault("fr_FR");
        $currencyType = new CurrencyType(
            array("EUR", "USD"),
            "EUR"
        );
        $form = $this->factory->create($currencyType, null, array());
        $form->setData(new Currency("USD"));
        $formView = $form->createView();

        $this->assertEquals("USD", $formView->children["tbbc_name"]->vars["value"]);
    }

}