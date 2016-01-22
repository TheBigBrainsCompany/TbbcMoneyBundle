<?php
namespace Tbbc\MoneyBundle\Pair\RatioProvider;

use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\RatioProviderInterface;

/**
 * Static ratio provider
 *
 * @author Pavel Dubinin <geekdevs@gmail.com>
 * @package Tbbc\MoneyBundle\Pair\RatioProvider
 */
class StaticRatioProvider implements RatioProviderInterface
{
    /**
     * @var array
     */
    private $ratios = array();

    /**
     * @param string $referenceCurrencyCode
     * @param string $currencyCode
     * @param float $ratio
     */
    public function setRatio($referenceCurrencyCode, $currencyCode, $ratio)
    {
        $pair = $this->getPairCode($referenceCurrencyCode, $currencyCode);
        $this->ratios[$pair] = $ratio;
    }

    /**
     * @inheritdoc
     */
    public function fetchRatio($referenceCurrencyCode, $currencyCode)
    {
        $pair = $this->getPairCode($referenceCurrencyCode, $currencyCode);

        if (!isset($this->ratios[$pair])) {
            throw new MoneyException('StaticRatioProvider does not have an exchange rate for '.$pair);
        }

        return $this->ratios[$pair];
    }

    /**
     * @param $referenceCurrencyCode
     * @param $currencyCode
     * @return string
     */
    private function getPairCode($referenceCurrencyCode, $currencyCode)
    {
        return $referenceCurrencyCode . '-' . $currencyCode;
    }
}
