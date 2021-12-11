<?php

namespace Container3VVPK3j;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/*
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getDoctrine_Orm_DefaultConfigurationService extends Tbbc_MoneyBundle_Tests_AppKernelTestContainer
{
    /*
     * Gets the private 'doctrine.orm.default_configuration' shared service.
     *
     * @return \Doctrine\ORM\Configuration
     */
    public static function do($container, $lazyLoad = true)
    {
        $container->privates['doctrine.orm.default_configuration'] = $instance = new \Doctrine\ORM\Configuration();

        $instance->setEntityNamespaces(['TbbcMoneyBundle' => 'Tbbc\\MoneyBundle\\Entity']);
        $instance->setMetadataCache(($container->privates['cache.doctrine.orm.default.metadata'] ?? $container->load('getCache_Doctrine_Orm_Default_MetadataService')));
        $instance->setQueryCacheImpl(($container->privates['cache.doctrine.orm.default.query.compatibility_layer'] ?? $container->load('getCache_Doctrine_Orm_Default_Query_CompatibilityLayerService')));
        $instance->setResultCacheImpl(($container->privates['cache.doctrine.orm.default.result.compatibility_layer'] ?? $container->load('getCache_Doctrine_Orm_Default_Result_CompatibilityLayerService')));
        $instance->setMetadataDriverImpl(($container->privates['.doctrine.orm.default_metadata_driver'] ?? $container->load('get_Doctrine_Orm_DefaultMetadataDriverService')));
        $instance->setProxyDir(($container->targetDir.''.'/doctrine/orm/Proxies'));
        $instance->setProxyNamespace('Proxies');
        $instance->setAutoGenerateProxyClasses(false);
        $instance->setClassMetadataFactoryName('Doctrine\\Bundle\\DoctrineBundle\\Mapping\\ClassMetadataFactory');
        $instance->setDefaultRepositoryClassName('Doctrine\\ORM\\EntityRepository');
        $instance->setNamingStrategy(($container->privates['doctrine.orm.naming_strategy.default'] ?? ($container->privates['doctrine.orm.naming_strategy.default'] = new \Doctrine\ORM\Mapping\DefaultNamingStrategy())));
        $instance->setQuoteStrategy(($container->privates['doctrine.orm.quote_strategy.default'] ?? ($container->privates['doctrine.orm.quote_strategy.default'] = new \Doctrine\ORM\Mapping\DefaultQuoteStrategy())));
        $instance->setEntityListenerResolver(($container->privates['doctrine.orm.default_entity_listener_resolver'] ?? ($container->privates['doctrine.orm.default_entity_listener_resolver'] = new \Doctrine\Bundle\DoctrineBundle\Mapping\ContainerEntityListenerResolver($container))));
        $instance->setRepositoryFactory(($container->privates['doctrine.orm.container_repository_factory'] ?? $container->load('getDoctrine_Orm_ContainerRepositoryFactoryService')));

        return $instance;
    }
}
