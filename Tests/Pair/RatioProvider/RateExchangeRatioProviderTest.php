<?php
namespace Tbbc\MoneyBundle\Tests\Pair\Storage;

use Money\Money;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\RatioProvider\RateExchangeRatioProvider;

/**
 * @group manager
 */
class RateExchangeRatioProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var  RateExchangeRatioProvider */
    protected $ratioProvider;

    public function setUp()
    {
        $this->ratioProvider = new RateExchangeRatioProvider();
    }

    public function tearDown()
    {
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
        try {
            $ratio = $this->ratioProvider->fetchRatio("ZZZ", "USD");
            $this->assertTrue(false);
        } catch (MoneyException $e) {
            $this->assertTrue(true);
        }
    }
}