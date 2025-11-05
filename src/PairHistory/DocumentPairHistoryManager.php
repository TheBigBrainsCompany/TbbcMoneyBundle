<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\PairHistory;

use DateTime;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Tbbc\MoneyBundle\Document\DocumentRatioHistory;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\SaveRatioEvent;

/**
 * Class DocumentPairHistoryManager.
 */
class DocumentPairHistoryManager implements PairHistoryManagerInterface
{
    public function __construct(protected DocumentManager $dm, protected string $referenceCurrencyCode)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getRatioAtDate(string $currencyCode, DateTimeInterface $savedAt): ?float
    {
        if ($currencyCode == $this->referenceCurrencyCode) {
            return 1.0;
        }

        $qb = $this->dm->createQueryBuilder();
        $qb->find(\Tbbc\MoneyBundle\Document\DocumentRatioHistory::class)
            ->field('currencyCode')->equals($currencyCode)
            ->field('savedAt')->lte($savedAt)
            ->sort('savedAt', 'DESC')
            ->limit(1)
        ;
        $query = $qb->getQuery();
        /** @var DocumentRatioHistory $ratioHistory */
        $ratioHistory = $query->getSingleResult();

        if (!($ratioHistory instanceof DocumentRatioHistory)) {
            return null;
        }

        if ($ratioHistory->getReferenceCurrencyCode() !== $this->referenceCurrencyCode) {
            throw new MoneyException('Reference currency code changed in history of currency ratio');
        }

        return $ratioHistory->getRatio();
    }

    /**
     * {@inheritdoc}
     */
    public function getRatioHistory(string $currencyCode, ?DateTimeInterface $startDate = null, ?DateTimeInterface $endDate = null): array
    {
        $qb = $this->dm->createQueryBuilder();
        $qb->find(\Tbbc\MoneyBundle\Document\DocumentRatioHistory::class)
            ->field('currencyCode')->equals($currencyCode)
            ->field('referenceCurrencyCode')->equals($this->referenceCurrencyCode)
            ->sort('savedAt', 'ASC')
        ;
        if ($startDate instanceof DateTime) {
            $qb->field('savedAt')->gte($startDate);
        }
        if ($endDate instanceof DateTime) {
            $qb->field('savedAt')->lte($endDate);
        }
        $query = $qb->getQuery();
        /** @var DocumentRatioHistory[] $resultList */
        $resultList = $query->execute();
        $res = [];

        foreach ($resultList as $ratioHistory) {
            $res[] = [
                'ratio' => $ratioHistory->getRatio(),
                'savedAt' => $ratioHistory->getSavedAt(),
            ];
        }

        return $res;
    }

    public function listenSaveRatioEvent(SaveRatioEvent $event): void
    {
        $ratioHistory = new DocumentRatioHistory();
        $ratioHistory->setReferenceCurrencyCode($event->getReferenceCurrencyCode());
        $ratioHistory->setCurrencyCode($event->getCurrencyCode());
        $ratioHistory->setRatio($event->getRatio());
        $ratioHistory->setSavedAt($event->getSavedAt());
        $this->dm->persist($ratioHistory);
        $this->dm->flush();
    }
}
