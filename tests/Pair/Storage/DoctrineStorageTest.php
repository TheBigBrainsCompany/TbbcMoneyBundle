<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Pair\Storage;

use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tbbc\MoneyBundle\Entity\DoctrineStorageRatio;
use Tbbc\MoneyBundle\Pair\Storage\DoctrineStorage;
use Tbbc\MoneyBundle\Tests\DatabaseTrait;

class DoctrineStorageTest extends KernelTestCase
{
    use DatabaseTrait;

    private ObjectManager $entityManager;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->doctrineStorage = new DoctrineStorage($this->entityManager, 'USD');
        $this->createDatabase();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->dropDatabase();
    }

    public function testLoadDefaultCurrency(): void
    {
        $ratioList = $this->doctrineStorage->loadRatioList();

        $this->assertCount(1, $ratioList);
        $this->assertArrayHasKey('USD', $ratioList);
        $this->assertSame(1.0, $ratioList['USD']);
    }

    public function testLoadForceOption(): void
    {
        $this->entityManager->persist(new DoctrineStorageRatio('USD', 1));
        $this->entityManager->flush();

        $this->assertCount(1, $this->doctrineStorage->loadRatioList());

        $storageRatio = new DoctrineStorageRatio('USD', 1);
        $storageRatio->setCurrencyCode('EUR');
        $storageRatio->setRatio(1.6);
        $this->entityManager->persist(new DoctrineStorageRatio('EUR', 1.6));
        $this->entityManager->flush();

        $this->assertCount(1, $this->doctrineStorage->loadRatioList());
        $this->assertCount(2, $this->doctrineStorage->loadRatioList(true));
        $ratioList = $this->doctrineStorage->loadRatioList();
        $this->assertSame(1.6, $ratioList['EUR']);
    }

    public function testSave(): void
    {
        $em = $this->entityManager;
        $repository = $em->getRepository(DoctrineStorageRatio::class);

        $this->doctrineStorage->saveRatioList([
            'EUR' => 1,
            'USD' => 1.6,
        ]);

        $this->assertCount(2, $repository->findAll());

        $this->doctrineStorage->saveRatioList([
            'EUR' => 1,
            'USD' => 1.6,
            'JPY' => 1.8,
        ]);

        $this->assertCount(3, $repository->findAll());

        $this->doctrineStorage->saveRatioList([
            'EUR' => 1,
        ]);

        $this->assertCount(1, $repository->findAll());
    }

    public function testSaveAndLoad(): void
    {
        $this->doctrineStorage->saveRatioList([
            'EUR' => 1,
            'USD' => 1.6,
        ]);

        $this->assertCount(2, $this->doctrineStorage->loadRatioList());
        $this->doctrineStorage->saveRatioList([
            'EUR' => 1,
            'USD' => 1.6,
            'JPY' => 2,
        ]);

        $this->assertCount(3, $this->doctrineStorage->loadRatioList());
    }
}
