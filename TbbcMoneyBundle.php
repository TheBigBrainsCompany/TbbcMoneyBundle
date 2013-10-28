<?php

namespace Tbbc\MoneyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Doctrine\DBAL\Types\Type;
use Tbbc\MoneyBundle\Type\MoneyType;

class TbbcMoneyBundle extends Bundle
{
    public function boot()
    {
        parent::boot();
        
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
