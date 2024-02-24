<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\PairHistory;

use Tbbc\MoneyBundle\Pair\SaveRatioEvent;

interface PairHistoryManagerInterface
{
    /**
     * returns the ratio of a currency at a given date.
     */
    public function getRatioAtDate(string $currencyCode, \DateTimeInterface $savedAt): ?float;

    /**
     * returns the list of all currency ratio saved between two dates.
     */
    public function getRatioHistory(string $currencyCode, \DateTimeInterface $startDate, \DateTimeInterface $endDate): array;

    public function listenSaveRatioEvent(SaveRatioEvent $event): void;
}
