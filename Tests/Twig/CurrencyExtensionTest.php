<?php
namespace Tbbc\MoneyBundle\Tests\Pair\Storage;

use Money\Currency;
use Tbbc\MoneyBundle\Twig\CurrencyExtension;

/**
 * @group filter
 */
class CurrencyExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var  CurrencyExtension */
    protected $ext;

    public function setUp()
    {
        \Locale::setDefault("fr_FR");
        $this->ext = new CurrencyExtension();
    }

//    public function testSymbol()
//    {
//        \Locale::setDefault("fr_FR");
//        $symbol = $this->ext->symbol(new Currency("EUR"));
//        $this->assertEquals('€', $symbol);
//    }
    public function testName()
    {
        $val = $this->ext->name(new Currency("EUR"));
        $this->assertEquals('EUR', $val);
    }
}