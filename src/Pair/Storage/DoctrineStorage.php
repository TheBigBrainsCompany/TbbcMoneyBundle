<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Pair\Storage;

use Doctrine\ORM\EntityManagerInterface;
use Tbbc\MoneyBundle\Entity\DoctrineStorageRatio;
use Tbbc\MoneyBundle\Pair\StorageInterface;

/**
 * Class DoctrineStorage.
 *
 * @author Philippe Le Van.
 */
class DoctrineStorage implements StorageInterface
{
    protected array $ratioList = [];

    public function __construct(protected EntityManagerInterface $entityManager, protected string $referenceCurrencyCode)
    {
    }

    public function loadRatioList(bool $force = false): array
    {
        if ((false === $force) && (count($this->ratioList) > 0)) {
            return $this->ratioList;
        }

        $repository = $this->entityManager->getRepository(DoctrineStorageRatio::class);
        $doctrineStorageRatios = $repository->findAll();

        if (0 === count($doctrineStorageRatios)) {
            $this->ratioList = [$this->referenceCurrencyCode => 1.0];
            $this->saveRatioList($this->ratioList);

            return $this->ratioList;
        }

        $this->ratioList = [];

        foreach ($doctrineStorageRatios as $doctrineStorageRatio) {
            if (
                null !== ($code = $doctrineStorageRatio->getCurrencyCode())
                && null !== ($ratio = $doctrineStorageRatio->getRatio())
            ) {
                $this->ratioList[$code] = $ratio;
            }
        }

        return $this->ratioList;
    }

    /**
     * @psalm-param array<string, null|float> $ratioList
     */
    public function saveRatioList(array $ratioList): void
    {
        $doctrineStorageRatios = $this->entityManager->getRepository(DoctrineStorageRatio::class)->findAll();

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
