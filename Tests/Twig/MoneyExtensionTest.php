<?php
namespace Tbbc\MoneyBundle\Tests\Pair\Storage;

use Money\Currency;
use Money\Money;
use Tbbc\MoneyBundle\Twig\MoneyExtension;

/**
 * @group filter
 */
class MoneyExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var  MoneyExtension */
    protected $ext;
    protected $pairManager;

    public function setUp()
    {
        \Locale::setDefault("fr_FR");
        $this->pairManager = $this->getMockBuilder('Tbbc\MoneyBundle\Pair\PairManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pairManager->expects($this->any())
            ->method('getReferenceCurrencyCode')
            ->will($this->returnValue("EUR"));

        $this->ext = new MoneyExtension($this->pairManager);
    }

    public function testFormatters()
    {
        $val = $this->ext->formatAmount(Money::EUR(123456));
        $this->assertEquals('1 234,56', $val);

        $val = $this->ext->asFloat(Money::EUR(123456));
        $this->assertEquals(1234.56, $val);
    }
}