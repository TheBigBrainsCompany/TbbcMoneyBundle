<?php
namespace Tbbc\MoneyBundle\PairHistory;

use Tbbc\MoneyBundle\Pair\SaveRatioEvent;

/**
 * Interface PairHistoryManagerInterface
 * @package Tbbc\MoneyBundle\PairHistory
 */
interface PairHistoryManagerInterface
{
    /**
     * returns the ratio of a currency at a given date
     *
     * @param string    $currencyCode
     * @param \DateTime $savedAt
     *
     * @return float
     */
    public function getRatioAtDate($currencyCode, \DateTime $savedAt);

    /**
     * returns the list of all currency ratio saved between two dates
     *
     * @param string    $currencyCode
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     *
     * @return array of {'savedAt'=>\DateTime, 'ratio' => float}
     */
    public function getRatioHistory($currencyCode, $startDate, $endDate);

    /**
     * @param SaveRatioEvent $event
     * @return void
     */
    public function listenSaveRatioEvent(SaveRatioEvent $event);
}
