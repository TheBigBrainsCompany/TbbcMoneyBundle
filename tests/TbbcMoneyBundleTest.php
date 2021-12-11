<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tbbc\MoneyBundle\TbbcMoneyBundle;

class TbbcMoneyBundleTest extends TestCase
{
    public function testBuild(): void
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->exactly(3))
            ->method('addCompilerPass');
        $bundle = new TbbcMoneyBundle();
        $bundle->build($container);
    }
}
