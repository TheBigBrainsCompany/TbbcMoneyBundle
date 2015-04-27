<?php

namespace Tbbc\MoneyBundle\Tests\Pair\Storage;

use Tbbc\MoneyBundle\Pair\RatioProvider\YahooFinanceRatioProvider;

/**
 * @author Hugues Maignol <hugues.maignol@kitpages.fr>
 * @group  manager
 */
class YahooFinanceRatioProviderTest extends AbstractRatioProviderTest
{
    /**
     * @inheritdoc
     */
    protected function getRatioProvider()
    {
        return new YahooFinanceRatioProvider();
    }
}
