<?php

namespace Tbbc\MoneyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Doctrine\DBAL\Types\Type;

use Tbbc\MoneyBundle\DependencyInjection\Compiler\PairHistoryCompilerPass;
use Tbbc\MoneyBundle\DependencyInjection\Compiler\RatioProviderCompilerPass;
use Tbbc\MoneyBundle\DependencyInjection\Compiler\StorageCompilerPass;
use Tbbc\MoneyBundle\Type\MoneyType;

class TbbcMoneyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new StorageCompilerPass());
        $container->addCompilerPass(new PairHistoryCompilerPass());
        $container->addCompilerPass(new RatioProviderCompilerPass());
    }
    
    public function boot()
    {
        parent::boot();
        
        if($this->container->hasParameter('doctrine.entity_managers')){
            if(!Type::hasType(MoneyType::NAME)) {
                Type::addType(MoneyType::NAME, 'Tbbc\MoneyBundle\Type\MoneyType');
            }

            $entityManagerNameList = $this->container->getParameter('doctrine.entity_managers');
            foreach($entityManagerNameList as $entityManagerName) {
                $em = $this->container->get($entityManagerName);
                if (!$em->getConnection()->getDatabasePlatform()->hasDoctrineTypeMappingFor(MoneyType::NAME)) {
                    $em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping(MoneyType::NAME, MoneyType::NAME);
                }
            }
        }
    }
}
