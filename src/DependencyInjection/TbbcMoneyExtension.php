<?php

declare(strict_types=1);

namespace Tbbc\MoneyBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TbbcMoneyExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('form_types.xml');
        $loader->load('twig_extension.xml');

        $this->remapParameters($config, $container, [
            'currencies' => 'tbbc_money.currencies',
            'reference_currency' => 'tbbc_money.reference_currency',
            'decimals' => 'tbbc_money.decimals',
            'enable_pair_history' => 'tbbc_money.enable_pair_history',
            'ratio_provider' => 'tbbc_money.ratio_provider',
        ]);

        $container->setParameter('tbbc_money.pair.storage', $config['storage']);
    }

    protected function remapParameters(array $config, ContainerBuilder $container, array $map): void
    {
        foreach ($map as $name => $paramName) {
            if (isset($config[$name])) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }
}
