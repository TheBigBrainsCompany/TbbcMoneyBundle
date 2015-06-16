<?php
/**
 * Created by Philippe Le Van.
 * Date: 04/07/13
 */

namespace Tbbc\MoneyBundle\Pair\Storage;

use Tbbc\MoneyBundle\Entity\DoctrineStorageRatio;
use Tbbc\MoneyBundle\Pair\StorageInterface;

use Doctrine\Common\Persistence\ObjectManager;

class DoctrineStorage implements StorageInterface
{

    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $objectManager = null;

    /** @var array  */
    protected $ratioList = array();

    /** @var  string */
    protected $referenceCurrencyCode;

    public function __construct(
        ObjectManager $objectManager,
        $referenceCurrencyCode
    )
    {
        $this->objectManager = $objectManager;
        $this->referenceCurrencyCode = $referenceCurrencyCode;
    }

    /**
     * @inheritdoc
     */
    public function loadRatioList($force = false)
    {
        if ( ($force === false) && (count($this->ratioList) > 0) ) {
            return $this->ratioList;
        }
        
        $repository = $this->objectManager->getRepository('Tbbc\MoneyBundle\Entity\DoctrineStorageRatio');
        /** @var DoctrineStorageRatio[] $doctrineStorageRatios */
        $doctrineStorageRatios = $repository->findAll();
        
        // FIXME
        // if filename doesn't exist, init with only reference currency code
        if (0 === count($doctrineStorageRatios)) {
            $currencyCodePair = $this->referenceCurrencyCode . '/' . $this->referenceCurrencyCode;
            $this->ratioList = array($currencyCodePair => (float) 1);
            $this->saveRatioList($this->ratioList);
            return $this->ratioList;
        }
        
        $this->ratioList = array();
        
        foreach ($doctrineStorageRatios as $doctrineStorageRatio) {
            $this->ratioList[$doctrineStorageRatio->getCurrencyCodePair()] = $doctrineStorageRatio->getRatio();
        }

        return $this->ratioList;
    }

    public function saveRatioList($ratioList)
    {
        $doctrineStorageRatios = $this->objectManager->getRepository('Tbbc\MoneyBundle\Entity\DoctrineStorageRatio')->findAll();
        
        foreach($doctrineStorageRatios as $doctrineStorageRatio) {
            $this->objectManager->remove($doctrineStorageRatio);
        }
        
        $this->objectManager->flush();
        
        foreach ($ratioList as $currencyCodePair => $ratio) {
            $this->objectManager->persist(new DoctrineStorageRatio($currencyCodePair, $ratio));
        }

        $this->objectManager->flush();
        $this->objectManager->clear();
        
        $this->ratioList = $ratioList;
    }
}
