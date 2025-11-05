<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Pair\Storage;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\Storage\CsvStorage;

class CsvStorageTest extends KernelTestCase
{
    protected CsvStorage $storage;
    protected string $fileName;

    public function setUp(): void
    {
        self::bootKernel();
        $this->fileName = self::getContainer()->getParameter('kernel.cache_dir') . '/pair.csv';
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }

        $this->storage = new CsvStorage($this->fileName, 'EUR');
    }

    public function tearDown(): void
    {
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }
    }

    public function testSave(): void
    {
        $ratioList = $this->storage->loadRatioList();
        $this->assertSame(
            "EUR;1\n",
            file_get_contents($this->fileName)
        );
        $ratioList['USD'] = 1.25;
        $this->storage->saveRatioList($ratioList);
        $this->assertSame(
            "EUR;1\nUSD;1.25\n",
            file_get_contents($this->fileName)
        );

        $ratioList = $this->storage->loadRatioList();
        $this->assertSame([
            'EUR' => 1.0,
            'USD' => 1.25,
        ], $ratioList);

        $this->storage->saveRatioList($ratioList);
        $this->assertSame(
            "EUR;1\nUSD;1.25\n",
            file_get_contents($this->fileName)
        );
    }

    public function testReadFromFile(): void
    {
        file_put_contents($this->fileName, "EUR;1\nUSD;1.25\n");
        $ratioList = $this->storage->loadRatioList();
        $this->assertSame([
            'EUR' => 1.0,
            'USD' => 1.25,
        ], $ratioList);
    }

    public function testCsvFileFailure(): void
    {
        $this->expectException(MoneyException::class);
        $this->expectExceptionMessage('error in ratioFileName ' . $this->fileName . ' on line 1, invalid argument count');
        file_put_contents($this->fileName, "1\nUSD;1.25\n");
        $this->storage->loadRatioList();
    }

    public function testUnknownValue(): void
    {
        $this->expectException(MoneyException::class);
        $this->expectExceptionMessage('error in ratioFileName ' . $this->fileName . ' on line 1, ratio is not a float or is null');
        file_put_contents($this->fileName, "EUR;abc\nUSD;1.25\n");
        $this->storage->loadRatioList();
    }

    public function testNegativeNumber(): void
    {
        $this->expectException(MoneyException::class);
        $this->expectExceptionMessage('error in ratioFileName ' . $this->fileName . ' on line 1, ratio has to be positive');
        file_put_contents($this->fileName, "EUR;-10\nUSD;1.25\n");
        $this->storage->loadRatioList();
    }

    public function testDoubleCurrency(): void
    {
        $this->expectException(MoneyException::class);
        $this->expectExceptionMessage('error in ratioFileName ' . $this->fileName . ' on line 2, ratio already exists for currency EUR');
        file_put_contents($this->fileName, "EUR;1\nEUR;1.25\n");
        $this->storage->loadRatioList();
    }
}
