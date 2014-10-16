<?php
namespace Tbbc\MoneyBundle\Tests\Pair\Storage;

use Tbbc\MoneyBundle\Pair\RatioProvider\GoogleRatioProvider;

/**
 * @author Hugues Maignol <hugues.maignol@kitpages.fr>
 * @group manager
 */
class GoogleRatioProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var  GoogleRatioProvider */
    protected $ratioProvider;

    public function setUp()
    {
        $this->ratioProvider = new GoogleRatioProvider();
    }

    public function testRetriveRatio()
    {
        $ratio = $this->ratioProvider->fetchRatio("EUR", "USD");
        $this->assertTrue(is_float($ratio));
        // note : I assume ratio between EUR and USD will remain between 0.3 and 3.
        // I believe that if it is not the case, this failing test won't be our biggest problem :-)
        $this->assertTrue($ratio > 0.3);
        $this->assertTrue($ratio < 3);
    }

    public function testException()
    {
        $this->setExpectedException('Tbbc\MoneyBundle\MoneyException');
        $this->ratioProvider->fetchRatio("ZZZ", "USD");
    }
}
