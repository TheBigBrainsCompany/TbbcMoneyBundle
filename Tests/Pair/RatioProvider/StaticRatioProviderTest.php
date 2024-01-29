<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Pair\RatioProvider;

use Tbbc\MoneyBundle\Pair\RatioProvider\StaticRatioProvider;
use Tbbc\MoneyBundle\Pair\RatioProviderInterface;

class StaticRatioProviderTest extends AbstractRatioProvider
{
    protected function getRatioProvider(): RatioProviderInterface
    {
        $provider = new StaticRatioProvider();
        $ratios = $this->getRatiosToTest();
        foreach ($ratios as $idx => $ratioData) {
            $ratio = $this->randomRatio($ratioData['ratio_min'], $ratioData['ratio_max'], $idx);
            $provider->setRatio(
                $ratioData['reference'],
                $ratioData['currency'],
                $ratio
            );
        }

        return $provider;
    }
}
