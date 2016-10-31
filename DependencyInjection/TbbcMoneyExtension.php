<?php

namespace Tbbc\MoneyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TbbcMoneyExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('form_types.xml');

        if (in_array('twig', $config['templating']['engines'])) {
            $loader->load('twig_extension.xml');
        }

        if (in_array('php', $config['templating']['engines'])) {
            $loader->load('templating_helper.xml');
        }

        $this->remapParameters($config, $container, array(
            'currencies'  => 'tbbc_money.currencies',
            'reference_currency'  => 'tbbc_money.reference_currency',
            'decimals'  => 'tbbc_money.decimals',
            'enable_pair_history'  => 'tbbc_money.enable_pair_history',
            'ratio_provider'  => 'tbbc_money.ratio_provider',
            'dbal_type_class'  => 'tbbc_money.dbal_type_class',
        ));

        $container->setParameter('tbbc_money.pair.storage', $config['storage']);
    }

    /**
     *
     * @param array            $config
     * @param ContainerBuilder $container
     * @param array            $map
     * @return void
     */
    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (isset($config[$name])) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }
}
