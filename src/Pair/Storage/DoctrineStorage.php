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
    /** @psalm-var array<string, null|float> */
    protected array $ratioList = [];

    public function __construct(protected EntityManagerInterface $entityManager, protected string $referenceCurrencyCode)
    {
    }

    /**
     * {@inheritdoc}
     */
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
     * {@inheritdoc}
     */
    public function saveRatioList(array $ratioList): void
    {
        /** @var DoctrineStorageRatio[] $doctrineStorageRatios */
        $doctrineStorageRatios = $this->entityManager->getRepository(DoctrineStorageRatio::class)->findAll();

        // index them in an associative array
        $existingStorageRatios = [];
        foreach ($doctrineStorageRatios as $doctrineStorageRatio) {
            if (null !== ($code = $doctrineStorageRatio->getCurrencyCode())) {
                $existingStorageRatios[$code] = $doctrineStorageRatio;
            }
        }

        foreach ($ratioList as $currencyCode => $ratio) {
            // load from existing, or create a new
            $existingStorageRatio = $existingStorageRatios[$currencyCode] ?? new DoctrineStorageRatio($currencyCode, $ratio);
            if (null !== $ratio) {
                $existingStorageRatio->setRatio($ratio);
                $this->entityManager->persist($existingStorageRatio);
            }

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
