<?php
/**
 * Created by Philippe Le Van.
 * Date: 01/07/13
 */

namespace Tbbc\MoneyBundle\Tests\Form\Type;

use Locale;
use Money\Money;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tbbc\MoneyBundle\Form\Type\SimpleMoneyType;

class SimpleMoneyTypeTest extends TypeTestCase
{
    private string $simpleMoneyTypeClass = 'Tbbc\MoneyBundle\Form\Type\SimpleMoneyType';

    public function testBindValid()
    {
        $form = $this->factory->create($this->simpleMoneyTypeClass, null, array());
        $form->submit(array(
            "tbbc_amount" => '12'
        ));
        $this->assertEquals(Money::EUR(1200), $form->getData());
    }

    public function testBindValidDecimals()
    {
        Locale::setDefault("fr_FR");
        $form = $this->factory->create($this->simpleMoneyTypeClass, null, array());
        $form->submit(array(
            "tbbc_amount" => '1,2'
        ));
        $this->assertEquals(Money::EUR(1200), $form->getData());
    }

    public function testBindDecimalValid()
    {
        Locale::setDefault("fr_FR");
        $form = $this->factory->create($this->simpleMoneyTypeClass, null, array());
        $form->submit(array(
            "tbbc_amount" => '12,5'
        ));
        $this->assertEquals(Money::EUR(1250), $form->getData());
    }

    public function testGreaterThan1000Valid()
    {
        Locale::setDefault("fr_FR");
        $form = $this->factory->create($this->simpleMoneyTypeClass, null, array());
        $form->submit(array(
            "tbbc_amount" => '1 252,5'
        ));
        $this->assertEquals(Money::EUR(125250), $form->getData());
    }

    public function testSetData()
    {
        Locale::setDefault("fr_FR");
        $form = $this->factory->create($this->simpleMoneyTypeClass, null, array());
        $form->setData(Money::EUR(120));
        $formView = $form->createView();

        $this->assertEquals("1,20", $formView->children["tbbc_amount"]->vars["value"]);
    }

    public function testOptions()
    {
        Locale::setDefault("fr_FR");
        $form = $this->factory->create($this->simpleMoneyTypeClass, null, array(
            'amount_options' => array(
                'label' => 'Amount',
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

        $this->factory->create($this->simpleMoneyTypeClass, null, array(
            'amount_options' => array(
                'this_does_not_exists' => 'Amount',
            ),
        ));
    }

    protected function getExtensions(): array
    {
        //This is probably not ideal, but I'm not sure how to set up the pair manager
        // with different decimals for different tests in Symfony 3.0
        $decimals = 2;
        $currencies = array('EUR', 'USD');
        $referenceCurrency = 'EUR';

        if ($this->getName() === "testBindValidDecimals")
            $decimals = 3;

        $pairManager = $this->getMockBuilder('Tbbc\MoneyBundle\Pair\PairManager')
            ->disableOriginalConstructor()
            ->getMock();
        $pairManager->expects($this->any())
            ->method('getReferenceCurrencyCode')
            ->will($this->returnValue("EUR"));

        return array(
            new PreloadedExtension(
                array(new SimpleMoneyType($decimals, $currencies, $referenceCurrency)), array()
            )
        );
    }

    public function testOverrideCurrency(): void
    {
        Locale::setDefault("fr_FR");
        $form = $this->factory->create($this->simpleMoneyTypeClass, null, ["currency" => "USD"]);
        $form->submit(array(
            "tbbc_amount" => '1 252,5'
        ));
        $this->assertEquals(Money::USD(125250), $form->getData());
    }

}
