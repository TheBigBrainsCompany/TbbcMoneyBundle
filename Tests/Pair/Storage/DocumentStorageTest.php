<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Pair\Storage;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Tbbc\MoneyBundle\Document\DocumentStorageRatio;
use Tbbc\MoneyBundle\Pair\Storage\DocumentStorage;
use Tbbc\MoneyBundle\Tests\DocumentDatabaseTrait;

final class DocumentStorageTest extends KernelTestCase
{
    use DocumentDatabaseTrait;

    private DocumentManager $documentManager;

    private DocumentStorage $documentStorage;

    public function setUp(): void
    {
        parent::setUp();
        self::$kernelOptions = [
            'environment' => 'testDocument',
            'configs' => [
                __DIR__ . '/../../config/document.yaml',
            ],
        ];
        self::bootKernel(self::$kernelOptions);
        $this->documentManager = self::getContainer()->get('doctrine_mongodb')->getManager();
        $this->documentStorage = new DocumentStorage($this->documentManager, 'USD');
        self::createDatabase();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->dropDatabase();
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        self::$class ??= self::getKernelClass();

        $env = $options['environment'] ?? $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'test';
        $debug = $options['debug'] ?? $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? true;
        $configs = $options['configs'] ?? [];

        return new self::$class($env, $debug, $configs);
    }

    public function testLoadDefaultCurrency(): void
    {
        $ratioList = $this->documentStorage->loadRatioList();

        $this->assertCount(1, $ratioList);
        $this->assertArrayHasKey('USD', $ratioList);
        $this->assertEqualsWithDelta(1.0, $ratioList['USD'], PHP_FLOAT_EPSILON);
    }

    public function testLoadForceOption(): void
    {
        $this->documentManager->persist(new DocumentStorageRatio('USD', 1));
        $this->documentManager->flush();

        $this->assertCount(1, $this->documentStorage->loadRatioList());

        $storageRatio = new DocumentStorageRatio('USD', 1);
        $storageRatio->setCurrencyCode('EUR');
        $storageRatio->setRatio(1.6);
        $this->documentManager->persist(new DocumentStorageRatio('EUR', 1.6));
        $this->documentManager->flush();

        $this->assertCount(1, $this->documentStorage->loadRatioList());
        $this->assertCount(2, $this->documentStorage->loadRatioList(true));
        $ratioList = $this->documentStorage->loadRatioList();
        $this->assertEqualsWithDelta(1.6, $ratioList['EUR'], PHP_FLOAT_EPSILON);
    }

    public function testSave(): void
    {
        $dm = $this->documentManager;
        $repository = $dm->getRepository(DocumentStorageRatio::class);

        $this->documentStorage->saveRatioList([
            'EUR' => 1,
            'USD' => 1.6,
        ]);

        $this->assertCount(2, $repository->findAll());

        $this->documentStorage->saveRatioList([
            'EUR' => 1,
            'USD' => 1.6,
            'JPY' => 1.8,
        ]);

        $this->assertCount(3, $repository->findAll());

        $this->documentStorage->saveRatioList([
            'EUR' => 1,
        ]);

        $this->assertCount(1, $repository->findAll());
    }

    public function testSaveAndLoad(): void
    {
        $this->documentStorage->saveRatioList([
            'EUR' => 1,
            'USD' => 1.6,
        ]);

        $this->assertCount(2, $this->documentStorage->loadRatioList());
        $this->documentStorage->saveRatioList([
            'EUR' => 1,
            'USD' => 1.6,
            'JPY' => 2,
        ]);

        $this->assertCount(3, $this->documentStorage->loadRatioList());
    }
}
