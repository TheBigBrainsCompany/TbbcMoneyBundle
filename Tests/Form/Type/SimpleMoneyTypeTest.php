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
use Money\Money;
use Tbbc\MoneyBundle\Form\Type\SimpleMoneyType;
use Tbbc\MoneyBundle\Pair\PairManager;

class SimpleMoneyTypeTest
    extends TypeTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->pairManager = $this->getMockBuilder('Tbbc\MoneyBundle\Pair\PairManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pairManager->expects($this->any())
            ->method('getReferenceCurrencyCode')
            ->will($this->returnValue("EUR"));
    }

    public function testBindValid()
    {
        $moneyType = new SimpleMoneyType($this->pairManager, 2);
        $form = $this->factory->create($moneyType, null, array());
        $form->bind(array(
            "tbbc_amount" => '12'
        ));
        $this->assertEquals(Money::EUR(1200), $form->getData());
    }
    public function testBindValidDecimals()
    {
        $moneyType = new SimpleMoneyType($this->pairManager, 3);
        $form = $this->factory->create($moneyType, null, array());
        $form->bind(array(
            "tbbc_amount" => '1,2'
        ));
        $this->assertEquals(Money::EUR(1200), $form->getData());
    }

    public function testBindDecimalValid()
    {
        \Locale::setDefault("fr_FR");
        $moneyType = new SimpleMoneyType($this->pairManager, 2);
        $form = $this->factory->create($moneyType, null, array());
        $form->bind(array(
            "tbbc_amount" => '12,5'
        ));
        $this->assertEquals(Money::EUR(1250), $form->getData());
    }

    public function testGreaterThan1000Valid()
    {
        \Locale::setDefault("fr_FR");
        $moneyType = new SimpleMoneyType($this->pairManager, 2);
        $form = $this->factory->create($moneyType, null, array());
        $form->bind(array(
            "tbbc_amount" => '1 252,5'
        ));
        $this->assertEquals(Money::EUR(125250), $form->getData());
    }

    public function testSetData()
    {
        \Locale::setDefault("fr_FR");
        $moneyType = new SimpleMoneyType($this->pairManager, 2);
        $form = $this->factory->create($moneyType, null, array());
        $form->setData(Money::EUR(120));
        $formView = $form->createView();

        $this->assertEquals("1,20", $formView->children["tbbc_amount"]->vars["value"]);
    }

}