<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Pair\RatioProvider;

use Exchanger\Exchanger;
use Exchanger\Service\PhpArray;
use InvalidArgumentException;
use Tbbc\MoneyBundle\MoneyException;
use Tbbc\MoneyBundle\Pair\RatioProvider\ExchangerAdapterRatioProvider;
use Tbbc\MoneyBundle\Pair\RatioProviderInterface;

class ExchangerAdapterRatioProviderTest extends AbstractRatioProvider
{
    protected function getRatioProvider(): RatioProviderInterface
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

    public function testInvalidCurrencyCode(): void
    {
        $this->expectException(MoneyException::class);
        $this->expectExceptionMessage('The currency code is an empty string');

        $ratiosSetup['EUR/123'] = $this->randomRatio(1, 3, 1);
        $service = new PhpArray($ratiosSetup);
        $exchanger = new Exchanger($service);
        $provider = new ExchangerAdapterRatioProvider($exchanger);
        $provider->fetchRatio('EUR', '');
    }
}
