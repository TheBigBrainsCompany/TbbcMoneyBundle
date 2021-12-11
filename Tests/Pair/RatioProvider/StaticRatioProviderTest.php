<?php

namespace Tbbc\MoneyBundle\Tests\Pair\RatioProvider;

use Tbbc\MoneyBundle\Pair\RatioProvider\StaticRatioProvider;
use Tbbc\MoneyBundle\Pair\RatioProviderInterface;
use Tbbc\MoneyBundle\Tests\Pair\Storage\AbstractRatioProviderTest;

/**
 * @author Pavel Dubinin <geekdevs@gmail.com>
 * @group  manager
 */
class StaticRatioProviderTest extends AbstractRatioProviderTest
{
    /**
     * @inheritdoc
     */
    protected function getRatioProvider(): RatioProviderInterface
    {
        return new StaticRatioProvider();
    }

    public function setUp(): void
    {
        parent::setUp();

        /**
         * @var StaticRatioProvider $ratioProvider
         */
        $ratioProvider = $this->ratioProvider;
        $ratios = $this->getRatiosToTest();

        foreach ($ratios as $idx=>$ratioData) {
            $ratio = $this->randomRatio($ratioData['ratio_min'], $ratioData['ratio_max'], $idx);

            $ratioProvider->setRatio(
                $ratioData['reference'],
                $ratioData['currency'],
                $ratio
            );
        }
    }

    public function testSetRatio()
    {
        $ratioProvider = $this->getRatioProvider();

        $ratioProvider->setRatio('EUR', 'USD', 1.23);
        $this->assertSame(1.23, $ratioProvider->fetchRatio('EUR', 'USD'));

        $ratioProvider->setRatio('EUR', 'USD', 1.67);
        $this->assertSame(1.67, $ratioProvider->fetchRatio('EUR', 'USD'));
    }

    /**
     * @param float $ratioMin
     * @param float $ratioMax
     * @param int $seed
     *
     * @return float
     */
    private function randomRatio(float $ratioMin, float $ratioMax, int $seed): float
    {
        $precision = 100;
        mt_srand($seed); //so that values are same across tests
        return mt_rand($ratioMin*$precision, $ratioMax*$precision) / $precision;
    }
}
