<?php
namespace Tbbc\MoneyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Doctrine\DBAL\Types\Type;
use Tbbc\MoneyBundle\Type\MoneyType;

/**
 * Class DoctrineTypeCompilerPass
 * @package Tbbc\MoneyBundle\DependencyInjection\Compiler
 */
class DoctrineTypeCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $parameter = 'doctrine.dbal.connection_factory.types';
        if (!$container->hasParameter($parameter)) {
            return;
        }

        $typeConfig = $container->getParameter($parameter);
        if (!isset($typeConfig[MoneyType::NAME])) {
            $typeConfig[MoneyType::NAME] = array(
                'class' => 'Tbbc\MoneyBundle\Type\MoneyType',
                'commented' => true,
            );
            $container->setParameter($parameter, $typeConfig);
        }

        foreach ($container->getServiceIds() as $name) {
            if (!preg_match('/^doctrine.dbal.\w+_connection$/', $name)) {
                continue;
            }
            $connection = $container->getDefinition($name);
            $mappingTypes = $connection->getArgument(3);
            if (isset($mappingTypes[MoneyType::NAME])) {
                continue;
            }
            $mappingTypes[MoneyType::NAME] = MoneyType::NAME;
            $connection->replaceArgument(3, $mappingTypes);
        }
    }
}
