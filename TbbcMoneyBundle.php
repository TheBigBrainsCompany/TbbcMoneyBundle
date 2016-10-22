<?php
namespace Tbbc\MoneyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tbbc\MoneyBundle\DependencyInjection\Compiler\PairHistoryCompilerPass;
use Tbbc\MoneyBundle\DependencyInjection\Compiler\RatioProviderCompilerPass;
use Tbbc\MoneyBundle\DependencyInjection\Compiler\StorageCompilerPass;
use Tbbc\MoneyBundle\DependencyInjection\Compiler\DoctrineTypeCompilerPass;

/**
 * Class TbbcMoneyBundle
 * @package Tbbc\MoneyBundle
 */
class TbbcMoneyBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new StorageCompilerPass());
        $container->addCompilerPass(new PairHistoryCompilerPass());
        $container->addCompilerPass(new RatioProviderCompilerPass());
        $container->addCompilerPass(new DoctrineTypeCompilerPass());
    }
}
