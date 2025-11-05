<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Pair\RatioProvider;

use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\RatioProviderInterface;

/**
 * Static ratio provider.
 *
 * @author Pavel Dubinin <geekdevs@gmail.com>
 */
class StaticRatioProvider implements RatioProviderInterface
{
    /**
     * @var float[]
     */
    private array $ratios = [];

    public function setRatio(string $referenceCurrencyCode, string $currencyCode, float $ratio): void
    {
        $pair = $this->getPairCode($referenceCurrencyCode, $currencyCode);
        $this->ratios[$pair] = $ratio;
    }

    public function fetchRatio(string $referenceCurrencyCode, string $currencyCode): float
    {
        $pair = $this->getPairCode($referenceCurrencyCode, $currencyCode);

        if (! isset($this->ratios[$pair])) {
            throw new MoneyException('StaticRatioProvider does not have an exchange rate for ' . $pair);
        }

        return $this->ratios[$pair];
    }

    private function getPairCode(string $referenceCurrencyCode, string $currencyCode): string
    {
        return $referenceCurrencyCode . '-' . $currencyCode;
    }
}
