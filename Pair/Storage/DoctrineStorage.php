<?php
namespace Tbbc\MoneyBundle\Pair\Storage;

use Tbbc\MoneyBundle\Entity\DoctrineStorageRatio;
use Tbbc\MoneyBundle\Pair\StorageInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class DoctrineStorage
 * @package Tbbc\MoneyBundle\Pair\Storage
 * @author Philippe Le Van.
 */
class DoctrineStorage implements StorageInterface
{
    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $objectManager = null;

    /** @var array  */
    protected $ratioList = array();

    /** @var  string */
    protected $referenceCurrencyCode;

    /**
     * DoctrineStorage constructor.
     *
     * @param ObjectManager $objectManager
     * @param string        $referenceCurrencyCode
     */
    public function __construct(ObjectManager $objectManager, $referenceCurrencyCode)
    {
        $this->objectManager = $objectManager;
        $this->referenceCurrencyCode = $referenceCurrencyCode;
    }

    /**
     * load and return ratioList
     *
     * @param  bool $force // force reload (no cache)
     * @return array
     *
     * @throws \Tbbc\MoneyBundle\MoneyException
     */
    public function loadRatioList($force = false)
    {
        if (($force === false) && (count($this->ratioList) > 0)) {
            return $this->ratioList;
        }

        $repository = $this->objectManager->getRepository('Tbbc\MoneyBundle\Entity\DoctrineStorageRatio');
        $doctrineStorageRatios = $repository->findAll();

        // FIXME
        // if filename doesn't exist, init with only reference currency code
        if (0 === count($doctrineStorageRatios)) {
            $this->ratioList = array($this->referenceCurrencyCode => 1.0);
            $this->saveRatioList($this->ratioList);

            return $this->ratioList;
        }

        $this->ratioList = array();

        foreach ($doctrineStorageRatios as $doctrineStorageRatio) {
            $this->ratioList[$doctrineStorageRatio->getCurrencyCode()] = $doctrineStorageRatio->getRatio();
        }

        return $this->ratioList;
    }

    /**
     * @param array $ratioList
     */
    public function saveRatioList($ratioList)
    {
        $doctrineStorageRatios = $this->objectManager->getRepository('Tbbc\MoneyBundle\Entity\DoctrineStorageRatio')->findAll();

        foreach ($doctrineStorageRatios as $doctrineStorageRatio) {
            $this->objectManager->remove($doctrineStorageRatio);
        }

        $this->objectManager->flush();

        foreach ($ratioList as $currencyCode => $ratio) {
            $this->objectManager->persist(new DoctrineStorageRatio($currencyCode, $ratio));
        }

        $this->objectManager->flush();
        $this->objectManager->clear();

        $this->ratioList = $ratioList;
    }
}
