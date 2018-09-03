<?php

namespace Tbbc\MoneyBundle\Tests\Twig\Extension;

use Money\Currency;
use Money\Money;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Tbbc\MoneyBundle\Twig\Extension\MoneyExtension;
use PHPUnit\Framework\TestCase;

/**
 * @author Benjamin Dulau <benjamin@thebigbrainscompany.com>
 */
class MoneyExtensionTest extends TestCase
{
    /**
     * @var MoneyExtension
     */
    private $extension;

    /**
     * @var array
     */
    protected $variables;

    public function setUp()
    {
        \Locale::setDefault("fr_FR");
        $pairManager = $this->getMockBuilder('Tbbc\MoneyBundle\Pair\PairManager')
            ->disableOriginalConstructor()
            ->getMock();
        $pairManager->expects($this->any())
            ->method('getReferenceCurrencyCode')
            ->will($this->returnValue("EUR"));

        $this->extension = new MoneyExtension(new MoneyFormatter(2), $pairManager);
        $this->variables = array('price' => new Money(123456789, new Currency('EUR')));
    }

    /**
     * @dataProvider getMoneyTests
     */
    public function testMoney($template, $expected)
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render($this->variables));
    }

    public function getMoneyTests()
    {
        return array(
            array('{{ price|money_localized_format }}', '1 234 567,89 €'),
            array('{{ price|money_localized_format("en_US") }}', '€1,234,567.89'),
            array('{{ price|money_format }}', '1 234 567,89 €'),
            array('{{ price|money_format(".", ",") }}', '1,234,567.89 €'),
            array('{{ price|money_format_amount }}', '1 234 567,89'),
            array('{{ price|money_format_amount(".", ",") }}', '1,234,567.89'),
            array('{{ price|money_format_currency }}', '€'),
            array('{{ price|money_as_float }}', '1234567.89'),
        );
    }

    protected function getTemplate($template)
    {
        $loader = new \Twig_Loader_Array(array('index' => $template));
        $twig = new \Twig_Environment($loader, array('debug' => true, 'cache' => false));
        $twig->addExtension($this->extension);

        return $twig->loadTemplate('index');
    }
}
