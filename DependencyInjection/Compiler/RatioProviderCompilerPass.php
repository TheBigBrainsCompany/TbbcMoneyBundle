<?php
namespace Tbbc\MoneyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RatioProviderCompilerPass
 * @package Tbbc\MoneyBundle\DependencyInjection\Compiler
 */
class RatioProviderCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $ratioProviderServiceName = $container->getParameter('tbbc_money.ratio_provider');

        $container->getDefinition('tbbc_money.pair_manager')->addMethodCall(
            'setRatioProvider',
            array(new Reference($ratioProviderServiceName))
        );
    }
}
