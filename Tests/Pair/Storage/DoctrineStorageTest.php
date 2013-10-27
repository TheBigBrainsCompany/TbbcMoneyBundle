<?php
namespace Tbbc\MoneyBundle\Tests\Pair\Storage;

use Tbbc\MoneyBundle\Tests\BundleOrmTestCase;
use Tbbc\MoneyBundle\Pair\Storage\DoctrineStorage;
use Tbbc\MoneyBundle\Entity\DoctrineStorageRatio;

/**
 * @group manager
 */
class DoctrineStorageTest extends BundleOrmTestCase
{
    /**
     * @var \Tbbc\MoneyBundle\Pair\Storage\DoctrineStorage
     */
    protected $doctrineStorage;

    public function setUp()
    {
        parent::setUp();
        
        $this->doctrineStorage = new DoctrineStorage($this->getEntityManager(), 'USD');
    }
    
    public function testLoadDefaultCurrency ()
    {
        $ratioList = $this->doctrineStorage->loadRatioList();

        $this->assertCount(1, $ratioList);
        $this->assertArrayHasKey('USD', $ratioList);
        $this->assertEquals(1, $ratioList['USD']);
    }
    
    public function testLoadForceOption ()
    {
        $this->getEntityManager()->persist(new DoctrineStorageRatio('USD', 1));
        $this->getEntityManager()->flush();
        
        $this->assertCount(1, $this->doctrineStorage->loadRatioList());

        $storageRatio = new DoctrineStorageRatio('USD', 1);
        $storageRatio->setCurrencyCode("EUR");
        $storageRatio->setRatio(1.6);
        $this->getEntityManager()->persist(new DoctrineStorageRatio('EUR', 1.6));
        $this->getEntityManager()->flush();
        
        $this->assertCount(1, $this->doctrineStorage->loadRatioList());
        $this->assertCount(2, $this->doctrineStorage->loadRatioList(true));
        $ratioList = $this->doctrineStorage->loadRatioList();
        $this->assertEquals(1.6, $ratioList["EUR"]);
    }

    public function testSave ()
    {
        $em = $this->getEntityManager();
        $repository = $em->getRepository('Tbbc\MoneyBundle\Entity\DoctrineStorageRatio');

        $this->doctrineStorage->saveRatioList(array (
            'EUR' => 1,
            'USD' => 1.6
        ));

        $this->assertCount(2, $repository->findAll());
        
        $this->doctrineStorage->saveRatioList(array (
            'EUR' => 1,
            'USD' => 1.6,
            'JPY' => 1.8
        ));
        
        $this->assertCount(3, $repository->findAll());
        
        $this->doctrineStorage->saveRatioList(array (
            'EUR' => 1
        ));
        
        $this->assertCount(1, $repository->findAll());
    }
    
    public function testSaveAndLoad ()
    {
        $this->doctrineStorage->saveRatioList(array (
            'EUR' => 1,
            'USD' => 1.6
        ));

        $this->assertCount(2, $this->doctrineStorage->loadRatioList());
        $this->doctrineStorage->saveRatioList(array (
            'EUR' => 1,
            'USD' => 1.6,
            'JPY' => 2
        ));

        $this->assertCount(3, $this->doctrineStorage->loadRatioList());
    }
}
