<?php
/**
 * Created by Philippe Le Van.
 * Date: 01/07/13
 */

namespace Tbbc\MoneyBundle\Tests\Form\Type;

use Money\Currency;
use Symfony\Component\Form\Test\FormIntegrationTestCase;
use Tbbc\MoneyBundle\Form\Type\CurrencyType;
use Tbbc\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\Test\TypeTestCase;
use Money\Money;

class MoneyTypeTest
    extends TypeTestCase
{
    public function testBindValid()
    {
        $currencyType = new CurrencyType(
            array("EUR", "USD"),
            "EUR"
        );
        $moneyType = new MoneyType($currencyType);
        $form = $this->factory->create($moneyType, null, array());
        $form->bind(array(
            "tbbc_currency" => array("tbbc_name"=>'EUR'),
            "tbbc_amount" => 100
        ));
        $this->assertEquals(Money::EUR(100), $form->getData());
    }

}