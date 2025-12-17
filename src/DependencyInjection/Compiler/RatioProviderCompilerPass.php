<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tbbc\MoneyBundle\Pair\PairManagerInterface;

/**
 * Class RatioProviderCompilerPass.
 */
class RatioProviderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $ratioProviderServiceName = (string) $container->getParameter('tbbc_money.ratio_provider');

        $container->getDefinition(PairManagerInterface::class)->addMethodCall(
            'setRatioProvider',
            [new Reference($ratioProviderServiceName)]
        );
    }
}
