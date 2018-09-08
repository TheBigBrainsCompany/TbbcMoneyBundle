<?php
namespace Tbbc\MoneyBundle\Tests\Pair\Storage;

use Tbbc\MoneyBundle\Pair\RatioProvider\ExchangerAdapterRatioProvider;
use Exchanger\Exchanger;
use Exchanger\Service\PhpArray;

/**
 * @author Pavel Dubinin <geekdevs@gmail.com>
 * @group  manager
 * @group  php5.5+
 */
class ExchangerAdapterRatioProviderTest extends AbstractRatioProviderTest
{
    /**
     * @inheritdoc
     */
    protected function getRatioProvider()
    {
        $ratios = $this->getRatiosToTest();

        $ratiosSetup = [];
        foreach ($ratios as $idx => $ratio) {
            $key = $ratio['reference'].'/'.$ratio['currency'];
            $ratiosSetup[$key] = $this->randomRatio($ratio['ratio_min'], $ratio['ratio_max'], $idx);
        }

        $service = new PhpArray($ratiosSetup);
        $exchanger = new Exchanger($service);

        return new ExchangerAdapterRatioProvider($exchanger);
    }

    /**
     * @param float $ratioMin
     * @param float $ratioMax
     * @param int $seed
     *
     * @return float
     */
    private function randomRatio($ratioMin, $ratioMax, $seed)
    {
        $precision = 100;
        mt_srand($seed); //so that values are same across tests
        return mt_rand($ratioMin*$precision, $ratioMax*$precision) / $precision;
    }
}
