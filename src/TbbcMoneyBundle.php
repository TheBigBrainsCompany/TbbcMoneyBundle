<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tbbc\MoneyBundle\DependencyInjection\Compiler\PairHistoryCompilerPass;
use Tbbc\MoneyBundle\DependencyInjection\Compiler\RatioProviderCompilerPass;
use Tbbc\MoneyBundle\DependencyInjection\Compiler\StorageCompilerPass;

/**
 * @psalm-suppress MissingConstructor
 */
class TbbcMoneyBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new StorageCompilerPass());
        $container->addCompilerPass(new PairHistoryCompilerPass());
        $container->addCompilerPass(new RatioProviderCompilerPass());
    }
}
