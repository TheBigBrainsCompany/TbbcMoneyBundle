<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Pair;

use Money\Money;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\PairManager;
use Tbbc\MoneyBundle\Pair\RatioProvider\StaticRatioProvider;
use Tbbc\MoneyBundle\Pair\Storage\CsvStorage;
use Tbbc\MoneyBundle\Tests\MoneyAssert;

class PairManagerTest extends KernelTestCase
{
    use MoneyAssert;

    protected PairManager $manager;

    protected string $fileName;

    public function setUp(): void
    {
        self::bootKernel();
        $this->fileName = self::getContainer()->getParameter('kernel.cache_dir') . '/pair.csv';
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }

        $storage = new CsvStorage($this->fileName, 'EUR');
        $dispatcher = $this->createMock(EventDispatcher::class);
        $this->manager = new PairManager(
            $storage,
            ['EUR', 'USD', 'CAD'],
            'EUR',
            $dispatcher
        );
    }

    public function tearDown(): void
    {
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }
    }

    public function testRatio(): void
    {
        $eur = Money::EUR(100);
        $sameEur = $this->manager->convert($eur, 'EUR');
        $this->assertMoneySame($eur, $sameEur);
        try {
            $this->manager->convert($eur, 'USD');
            $this->fail();
        } catch (MoneyException $e) {
            $this->assertTrue(true);
        }
        $this->manager->saveRatio('USD', 1.25);
        $usd = $this->manager->convert($eur, 'USD');
        $this->assertMoneySame(Money::USD(125), $usd);

        $this->manager->saveRatio('CAD', 1.50);
        $cad = $this->manager->convert($usd, 'CAD');
        $this->assertMoneySame(Money::CAD(150), $cad);
    }

    public function testSave(): void
    {
        $this->manager->saveRatio('USD', 1.25);
        $this->assertSame(
            "EUR;1\nUSD;1.25\n",
            file_get_contents($this->fileName)
        );
        $this->manager->saveRatio('CAD', 1.50);
        $this->assertSame(
            "EUR;1\nUSD;1.25\nCAD;1.5\n",
            file_get_contents($this->fileName)
        );
        $this->assertSame(
            [
                'EUR' => 1.0,
                'USD' => 1.25,
                'CAD' => 1.5,
            ],
            $this->manager->getRatioList()
        );
    }

    public function testCurrencyCodeList(): void
    {
        $this->assertSame(
            ['EUR', 'USD', 'CAD'],
            $this->manager->getCurrencyCodeList()
        );
    }

    public function testRatioExceptions(): void
    {
        $this->expectException(MoneyException::class);
        $this->manager->saveRatio('USD', -2.3);
    }

    public function testCurrencyWithoutRatio(): void
    {
        $this->expectException(MoneyException::class);
        $eur = Money::BSD(100);
        $this->manager->convert($eur, 'EUR');
    }

    public function testRatioProvider(): void
    {
        //Provider
        $provider = new StaticRatioProvider();
        $provider->setRatio('EUR', 'USD', 1.08);
        $provider->setRatio('EUR', 'CAD', 1.54);

        //Store rates in manager
        $this->manager->setRatioProvider($provider);
        $this->manager->saveRatioListFromRatioProvider();

        //Check saved rates
        $this->assertSame(1.08, $this->manager->getRelativeRatio('EUR', 'USD'));
        $this->assertSame(1.54, $this->manager->getRelativeRatio('EUR', 'CAD'));

        //Change provider rates and make sure stored rates are not touched
        $provider->setRatio('EUR', 'USD', 2.2);
        $provider->setRatio('EUR', 'CAD', 1.83);
        $this->assertSame(1.08, $this->manager->getRelativeRatio('EUR', 'USD'));
        $this->assertSame(1.54, $this->manager->getRelativeRatio('EUR', 'CAD'));
    }

    public function testNoRatioProvider(): void
    {
        $this->expectException(MoneyException::class);

        $this->manager->saveRatioListFromRatioProvider();
    }
}
