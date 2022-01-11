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
        $doctrineStorageRatios = $this->entityManager->getRepository('Tbbc\MoneyBundle\Entity\DoctrineStorageRatio')->findAll();

        // do it all in a transaction to avoid concurrency issue while we insert new ones
        $this->entityManager->transactional(function($em) use ($doctrineStorageRatios, $ratioList) {
            // first remove all existing ratios
            foreach ($doctrineStorageRatios as $doctrineStorageRatio) {
                $em->remove($doctrineStorageRatio);
            }
            // then add new ones
            foreach ($ratioList as $currencyCode => $ratio) {
                $em->persist(new DoctrineStorageRatio($currencyCode, $ratio));
            }
        });

        // flush to database to do remove and insert in one transaction
        $this->entityManager->flush();
        $this->entityManager->clear();

        $this->ratioList = $ratioList;
    }
}
