<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\DependencyInjection\Compiler;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
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
        $storage = $container->getParameter('tbbc_money.pair.storage');

        if (true !== $enabled) {
            return;
        }

        //Determine if DoctrineBundle or DoctrineMongoDBBundle is defined
        if (!(isset($bundles['DoctrineBundle']) || isset($bundles['DoctrineMongoDBBundle']))) {
            throw new \RuntimeException('TbbcMoneyBundle - DoctrineBundle or DoctrineMongoDBBundle is needed to use the pair history function');
        }

        //Use DoctrineBundle
        if ('doctrine' === $storage) {
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

        //Use DoctrineMongoDBBundle
        if ('document' === $storage) {
            $pairHistoryDefinition = new Definition(\Tbbc\MoneyBundle\PairHistory\DocumentPairHistoryManager::class, [
                new Reference('doctrine_mongodb.odm.document_manager'),
                $container->getParameter('tbbc_money.reference_currency'),
            ]);
            $pairHistoryDefinition->setPublic(true);

            $pairHistoryDefinition->addTag('kernel.event_listener', [
                'event' => 'tbbc_money.after_ratio_save',
                'method' => 'listenSaveRatioEvent',
            ]);

            $container->setDefinition('tbbc_money.pair_history_manager', $pairHistoryDefinition);

            //Add document schema mappings
            $modelDir = (string) realpath(__DIR__.'/../../Resources/config/document/ratios');
            $path = DoctrineMongoDBMappingsPass::createXmlMappingDriver([
                $modelDir => 'Tbbc\MoneyBundle\Document',
            ], [
            ]);
            $path->process($container);
        }
    }
}
