<?php
namespace Tbbc\MoneyBundle\Tests\Pair;

use Money\Money;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\PairManager;
use Tbbc\MoneyBundle\Pair\RatioProvider\YahooFinanceRatioProvider;
use Tbbc\MoneyBundle\Pair\Storage\CsvStorage;

/**
 * @group manager
 */
class PairManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var  PairManager */
    protected $manager;
    protected $fileName;
    public function setUp()
    {
        $this->fileName = __DIR__."/../app/data/tbbc_money/pair.csv";
        $dir = dirname($this->fileName);
        exec("rm -rf ".escapeshellarg($dir));
        $storage = new CsvStorage($this->fileName, "EUR");
        // create EventDispatcher mock
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');

        $this->manager = new PairManager(
            $storage,
            array("EUR", "USD", "CAD"),
            "EUR",
            $dispatcher
        );
    }

    public function tearDown()
    {
        $dir = dirname($this->fileName);
        exec("rm -rf ".escapeshellarg($dir));
    }

    public function testRatio()
    {
        $eur = Money::EUR(100);
        $sameEur = $this->manager->convert($eur, "EUR");
        $this->assertEquals(Money::EUR(100), $sameEur);
        try {
            $this->manager->convert($eur, "USD");
            $this->assertTrue(false);
        } catch (MoneyException $e) {
            $this->assertTrue(true);
        }
        $this->manager->saveRatio("USD", 1.25);
        $usd = $this->manager->convert($eur, "USD");
        $this->assertEquals(Money::USD(125), $usd);

        $this->manager->saveRatio("CAD", 1.50);
        $cad = $this->manager->convert($usd, "CAD");
        $this->assertEquals(Money::CAD(150), $cad);
    }

    public function testSave()
    {
        $this->manager->saveRatio("USD", 1.25);
        $this->assertEquals(
            "EUR;1\nUSD;1.25\n",
            file_get_contents($this->fileName)
        );
        $this->manager->saveRatio("CAD", 1.50);
        $this->assertEquals(
            "EUR;1\nUSD;1.25\nCAD;1.5\n",
            file_get_contents($this->fileName)
        );
        $this->assertEquals(
            array(
                'USD' => 1.25,
                'EUR' => 1.0,
                'CAD' => 1.5
            ),
            $this->manager->getRatioList()
        );
    }

    public function testCurrencyCodeList()
    {
        $this->assertEquals(
            array("EUR", "USD", "CAD"),
            $this->manager->getCurrencyCodeList()
        );
    }

    /**
     * @expectedException \Tbbc\MoneyBundle\MoneyException
     */
    public function testRatioExceptions()
    {
        $this->manager->saveRatio("USD", -2.3);
    }
    /**
     * @expectedException \Tbbc\MoneyBundle\MoneyException
     */
    public function testCurrencyWithoutRatio()
    {
        $eur = Money::BSD(100);
        $bsd = $this->manager->convert($eur, "EUR");
    }

    public function testRatioProvider()
    {
        $provider = new YahooFinanceRatioProvider();
        $this->manager->setRatioProvider($provider);
        $this->manager->saveRatioListFromRatioProvider();
        $ratio = $this->manager->getRelativeRatio("EUR", "USD");
        $this->assertTrue($ratio > 0.3);
        $this->assertTrue($ratio < 3);
        $referenceRatio = $provider->fetchRatio("EUR", "CAD");
        //The currency exchange rate are updated every seconds
        //The round is a workaround to negate the variation between the two ratio fetches
        $this->assertEquals(
            ROUND($referenceRatio, 2),
            ROUND($this->manager->getRelativeRatio("EUR", "CAD"),2)
        );
    }

    /**
     * @expectedException \Tbbc\MoneyBundle\MoneyException
     */
    public function testNoRatioProvider()
    {
        $this->manager->saveRatioListFromRatioProvider();
    }
}
