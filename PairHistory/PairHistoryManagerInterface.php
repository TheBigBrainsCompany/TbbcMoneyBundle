<?php
namespace Tbbc\MoneyBundle\PairHistory;

use Money\Currency;
use Tbbc\MoneyBundle\Pair\SaveRatioEvent;

interface PairHistoryManagerInterface
{
    /**
     * returns the ratio of a currency at a given date
     *
     * @param string|Currency $currencyTo
     * @param \DateTime $savedAt
     * @param string|Currency|null $currencyFrom
     * @return float
     */
    public function getRatioAtDate($currencyTo, \DateTime $savedAt, $currencyFrom = null);

    /**
     * returns the list of all currency ratio saved between two dates
     *
     * @param string|Currency $currencyTo
     * @param \DateTime|mixed $startDate
     * @param \DateTime|mixed $endDate
     * @param string|Currency|null $currencyFrom
     * @return array of {'savedAt'=>\DateTime, 'ratio' => float}
     */
    public function getRatioHistory($currencyTo, $startDate, $endDate, $currencyFrom = null);

    /**
     * @param SaveRatioEvent $event
     * @return void
     */
    public function listenSaveRatioEvent(SaveRatioEvent $event);
}