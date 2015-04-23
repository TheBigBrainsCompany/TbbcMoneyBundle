<?php

namespace Tbbc\MoneyBundle\Tests\Pair\Storage;

use Tbbc\MoneyBundle\Pair\RatioProvider\GoogleRatioProvider;

/**
 * @author Hugues Maignol <hugues.maignol@kitpages.fr>
 * @group  manager
 */
class GoogleRatioProviderTest extends AbstractRatioProviderTest
{
    /**
     * @inheritdoc
     */
    protected function getRatioProvider()
    {
        return new GoogleRatioProvider();
    }
}
