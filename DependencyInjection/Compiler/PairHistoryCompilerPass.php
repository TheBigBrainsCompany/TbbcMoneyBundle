<?php
namespace Tbbc\MoneyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class PairHistoryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        $enabled = $container->getParameter('tbbc_money.enable_pair_history');
        
        //Determine if DoctrineBundle is defined
        if (true === $enabled) {
            if(!isset($bundles['DoctrineBundle'])) {
                throw new \RuntimeException('TbbcMoneyBundle - DoctrineBundle is needed to use the pair history function');
            }
            
            $pairHistoryDefinition = new Definition('Tbbc\MoneyBundle\PairHistory\PairHistoryManager', array(
                new Reference('doctrine.orm.entity_manager'),
                $container->getParameter('tbbc_money.reference_currency')
            ));
            $pairHistoryDefinition->addTag('kernel.event_listener',array(
                'event' => 'tbbc_money.after_ratio_save',
                'method' => 'listenSaveRatioEvent'
            ));

            $container->setDefinition('tbbc_money.pair_history_manager', $pairHistoryDefinition);
        }
    }
}
