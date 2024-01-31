<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests\Pair\RatioProvider;

use Money\Currency;
use PHPUnit\Framework\TestCase;
use Tbbc\MoneyBundle\Pair\RatioProviderInterface;
use Tbbc\MoneyBundle\MoneyException;

/**
 * This class can be used to easily test your custom ratio providers.
 *
 * @author Hugues Maignol <hugues.maignol@kitpages.fr>
 */
abstract class AbstractRatioProvider extends TestCase
{
    /**
     * The currently tested RatioProvider.
     */
    protected RatioProviderInterface $ratioProvider;

    public function setUp(): void
    {
        $this->ratioProvider = $this->getRatioProvider();
    }

    public function testRatioFetching(): void
    {
        foreach ($this->getRatiosToTest() as $testParameters) {
            $ratio = $this->ratioProvider->fetchRatio($testParameters['reference'], $testParameters['currency']);
            $this->assertIsFloat($ratio, 'The fetched ratio must be a float');
            $this->assertLessThan(
                $testParameters['ratio_max'],
                $ratio,
                'The ratio is too high, are wee in deep economical crisis ?'
            );
            $this->assertGreaterThan(
                $testParameters['ratio_min'],
                $ratio,
                'The ratio is too low'
            );
        }
    }

    public function testExceptionForUnknownCurrency(): void
    {
        $this->expectException(MoneyException::class);
        $this->ratioProvider->fetchRatio('ZZZ', 'USD');
    }

    /**
     * Returns the instanciated RatioProvider service that will be tested.
     */
    abstract protected function getRatioProvider(): RatioProviderInterface;

    /**
     * Each array value returned is an array with the keys :
     *  - reference : The base currency for the ratio
     *  - currency : The currency for which we want the ratio
     *  - ratio_min : The minimum ratio value considered valid
     *  - ratio_max : The maximum ratio value considered valid.
     */
    protected function getRatiosToTest(): array
    {
        return [
            [
                'reference' => 'EUR',
                'currency' => 'USD',
                'ratio_min' => 0.3,
                'ratio_max' => 3,
            ],
            [
                'reference' => 'GBP',
                'currency' => 'EUR',
                'ratio_min' => 0.3,
                'ratio_max' => 3,
            ],
        ];
    }

    protected function randomRatio(float $ratioMin, float $ratioMax, int $seed): float
    {
        $precision = 100;
        mt_srand($seed);

        $float = random_int(
                ((int) $ratioMin * $precision),
                ((int) $ratioMax * $precision)
            ) / $precision;

        if ($float <= 0.3) {
            $float = 0.31;
        }

        if ($float >= 3) {
            $float = 2.99;
        }

        return $float;
    }
}
