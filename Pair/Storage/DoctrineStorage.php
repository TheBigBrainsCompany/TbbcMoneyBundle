<?php
namespace Tbbc\MoneyBundle\Pair\Storage;

use Doctrine\ORM\EntityManagerInterface;
use Tbbc\MoneyBundle\Entity\DoctrineStorageRatio;
use Tbbc\MoneyBundle\Pair\StorageInterface;

/**
 * Class DoctrineStorage
 * @package Tbbc\MoneyBundle\Pair\Storage
 * @author Philippe Le Van.
 */
class DoctrineStorage implements StorageInterface
{
    /** @var EntityManagerInterface */
    protected $entityManager = null;

    /** @var array  */
    protected $ratioList = array();

    /** @var  string */
    protected $referenceCurrencyCode;

    /**
     * DoctrineStorage constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param string        $referenceCurrencyCode
     */
    public function __construct(EntityManagerInterface $entityManager, $referenceCurrencyCode)
    {
        $this->entityManager = $entityManager;
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


        $repository = $this->entityManager->getRepository('Tbbc\MoneyBundle\Entity\DoctrineStorageRatio');
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
        /** @var DoctrineStorageRatio[] $doctrineStorageRatios */
        $doctrineStorageRatios = $this->entityManager->getRepository('Tbbc\MoneyBundle\Entity\DoctrineStorageRatio')->findAll();

        // index them in an associative array
        $existingStorageRatios = [];
        foreach ($doctrineStorageRatios as $doctrineStorageRatio) {
            $existingStorageRatios[$doctrineStorageRatio->getCurrencyCode()] = $doctrineStorageRatio;
        }

        // for each ratio to save
        foreach ($ratioList as $currencyCode => $ratio) {
            // load from existing, or create a new
            $existingStorageRatio = isset($existingStorageRatios[$currencyCode])
                ? $existingStorageRatios[$currencyCode]
                : new DoctrineStorageRatio($currencyCode, $ratio)
            ;

            // update it (not really needed if we just created it)
            $existingStorageRatio->setRatio($ratio);
            // persist (not really needed if we loaded from doctrine)
            $this->entityManager->persist($existingStorageRatio);

            // remove from the array, as we do not want to remove this one
            unset($existingStorageRatios[$currencyCode]);
        }

        // remove the remaining ones
        foreach ($existingStorageRatios as $doctrineStorageRatio) {
            $this->entityManager->remove($doctrineStorageRatio);
        }

        // flush to database
        $this->entityManager->flush();
        $this->entityManager->clear();

        $this->ratioList = $ratioList;
    }
}
