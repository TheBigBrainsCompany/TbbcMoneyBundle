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
    /** @var EntityManagerInterface|null */
    protected $entityManager = null;

    /** @var array  */
    protected $ratioList = [];

    /** @var  string */
    protected $referenceCurrencyCode;

    /**
     * DoctrineStorage constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param string $referenceCurrencyCode
     */
    public function __construct(EntityManagerInterface $entityManager, string $referenceCurrencyCode)
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
    public function loadRatioList(bool $force = false): array
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
    public function saveRatioList(array $ratioList)
    {
        $doctrineStorageRatios = $this->entityManager->getRepository('Tbbc\MoneyBundle\Entity\DoctrineStorageRatio')->findAll();

        foreach ($doctrineStorageRatios as $doctrineStorageRatio) {
            $this->entityManager->remove($doctrineStorageRatio);
        }

        $this->entityManager->flush();

        foreach ($ratioList as $currencyCode => $ratio) {
            $this->entityManager->persist(new DoctrineStorageRatio($currencyCode, $ratio));
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        $this->ratioList = $ratioList;
    }
}
