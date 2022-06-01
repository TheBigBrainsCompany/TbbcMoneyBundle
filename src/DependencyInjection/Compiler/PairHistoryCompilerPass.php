<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\DependencyInjection\Compiler;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class PairHistoryCompilerPass.
 */
class PairHistoryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');
        $enabled = $container->getParameter('tbbc_money.enable_pair_history');

        //Determine if DoctrineBundle is defined
        if (true === $enabled) {
            if (!isset($bundles['DoctrineBundle'])) {
                throw new \RuntimeException('TbbcMoneyBundle - DoctrineBundle is needed to use the pair history function');
            }

            $pairHistoryDefinition = new Definition(\Tbbc\MoneyBundle\PairHistory\PairHistoryManager::class, [
                new Reference('doctrine.orm.entity_manager'),
                $container->getParameter('tbbc_money.reference_currency'),
            ]);
            $pairHistoryDefinition->setPublic(true);

            $pairHistoryDefinition->addTag('kernel.event_listener', [
                'event' => 'tbbc_money.after_ratio_save',
                'method' => 'listenSaveRatioEvent',
            ]);

            $container->setDefinition('tbbc_money.pair_history_manager', $pairHistoryDefinition);

            //Add doctrine schema mappings
            $modelDir = (string) realpath(__DIR__.'/../../Resources/config/doctrine/ratios');
            $path = DoctrineOrmMappingsPass::createXmlMappingDriver([
                $modelDir => 'Tbbc\MoneyBundle\Entity',
            ]);
            $path->process($container);
        }
    }
}
