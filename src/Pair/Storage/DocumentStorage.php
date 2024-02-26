<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Pair\Storage;

use Doctrine\ODM\MongoDB\DocumentManager;
use Tbbc\MoneyBundle\Document\DocumentStorageRatio;
use Tbbc\MoneyBundle\Pair\StorageInterface;

/**
 * Class DocumentStorage.
 *
 * @author Philippe Le Van.
 */
class DocumentStorage implements StorageInterface
{
    /** @psalm-var array<string, null|float> */
    protected array $ratioList = [];

    public function __construct(protected DocumentManager $documentManager, protected string $referenceCurrencyCode)
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

        $repository = $this->documentManager->getRepository(DocumentStorageRatio::class);
        $documentStorageRatios = $repository->findAll();

        if (0 === count($documentStorageRatios)) {
            $this->ratioList = [$this->referenceCurrencyCode => 1.0];
            $this->saveRatioList($this->ratioList);

            return $this->ratioList;
        }

        $this->ratioList = [];

        foreach ($documentStorageRatios as $documentStorageRatio) {
            if (
                null !== ($code = $documentStorageRatio->getCurrencyCode())
                && null !== ($ratio = $documentStorageRatio->getRatio())
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
        $documentStorageRatios = $this->documentManager->getRepository(DocumentStorageRatio::class)->findAll();

        // index them in an associative array
        $existingStorageRatios = [];
        foreach ($documentStorageRatios as $documentStorageRatio) {
            if (null !== ($code = $documentStorageRatio->getCurrencyCode())) {
                $existingStorageRatios[$code] = $documentStorageRatio;
            }
        }

        foreach ($ratioList as $currencyCode => $ratio) {
            // load from existing, or create a new
            $existingStorageRatio = $existingStorageRatios[$currencyCode] ?? new DocumentStorageRatio($currencyCode, $ratio);
            if (null !== $ratio) {
                $existingStorageRatio->setRatio($ratio);
                $this->documentManager->persist($existingStorageRatio);
            }

            // remove from the array, as we do not want to remove this one
            unset($existingStorageRatios[$currencyCode]);
        }

        // remove the remaining ones
        foreach ($existingStorageRatios as $documentStorageRatio) {
            $this->documentManager->remove($documentStorageRatio);
        }

        // flush to database
        $this->documentManager->flush();
        $this->documentManager->clear();

        $this->ratioList = $ratioList;
    }
}
