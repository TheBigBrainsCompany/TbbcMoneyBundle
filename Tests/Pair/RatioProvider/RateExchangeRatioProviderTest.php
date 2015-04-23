<?php

namespace Tbbc\MoneyBundle\Tests\Pair\Storage;

use Tbbc\MoneyBundle\Pair\RatioProvider\RateExchangeRatioProvider;

/**
 * @group manager
 */
class RateExchangeRatioProviderTest extends AbstractRatioProviderTest
{
    /**
     * @inheritdoc
     */
    protected function getRatioProvider()
    {
        return new RateExchangeRatioProvider();
    }
}
