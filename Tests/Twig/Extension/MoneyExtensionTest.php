<?php

namespace Tbbc\MoneyBundle\Tests\Twig\Extension;

use Money\Currency;
use Money\Money;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Tbbc\MoneyBundle\Twig\Extension\MoneyExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

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
    public function testMoney($template, $expected, $errorMessage)
    {
        $this->assertSame($expected, $this->getTemplate($template)->render($this->variables), $errorMessage);
    }

    public function getMoneyTests()
    {
        return array(
            array('{{ price|money_localized_format }}', '1 234 567,89 €', '1'),
            array('{{ price|money_localized_format("en_US") }}', '€1,234,567.89', '2'),
            array('{{ price|money_format }}', '1 234 567,89 €', '3'),
            array('{{ price|money_format(".", ",") }}', '1,234,567.89 €', '4'),
            array('{{ price|money_format_amount }}', '1 234 567,89', '5'),
            array('{{ price|money_format_amount(".", ",") }}', '1,234,567.89', '6'),
            array('{{ price|money_format_currency }}', '€', '7'),
            array('{{ price|money_as_float }}', '1234567.89', '8'),
        );
    }

    protected function getTemplate($template)
    {
        $loader = new ArrayLoader(array('index' => $template));
        $twig = new Environment($loader, array('debug' => true, 'cache' => false));
        $twig->addExtension($this->extension);

        return $twig->loadTemplate('index');
    }
}
