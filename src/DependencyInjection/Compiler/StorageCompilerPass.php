<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\DependencyInjection\Compiler;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class StorageCompilerPass.
 */
class StorageCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');
        $storage = $container->getParameter('tbbc_money.pair.storage');

        //Determine if DoctrineBundle is defined
        if ('doctrine' === $storage) {
            if (!isset($bundles['DoctrineBundle'])) {
                throw new \RuntimeException('TbbcMoneyBundle - DoctrineBundle is needed to use Doctrine as a storage');
            }

            //Add doctrine schema mappings
            $modelDir = (string) realpath(__DIR__.'/../../Resources/config/doctrine/ratios');
            $path = DoctrineOrmMappingsPass::createXmlMappingDriver([
                $modelDir => 'Tbbc\MoneyBundle\Entity',
            ]);
            $path->process($container);

            $storageDoctrineDefinition = new Definition(\Tbbc\MoneyBundle\Pair\Storage\DoctrineStorage::class, [
                new Reference('doctrine.orm.entity_manager'),
                $container->getParameter('tbbc_money.reference_currency'),
            ]);

            $container->setDefinition('tbbc_money.pair.doctrine_storage', $storageDoctrineDefinition);
            $container->getDefinition('tbbc_money.pair_manager')->replaceArgument(0, new Reference('tbbc_money.pair.doctrine_storage'));
        }
    }
}
