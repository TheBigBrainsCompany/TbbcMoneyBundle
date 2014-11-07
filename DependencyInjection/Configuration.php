<?php

namespace Tbbc\MoneyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('tbbc_money');
        $this->addCurrencySection($rootNode);

        return $treeBuilder;
    }

    /**
     * Parses the kitpages_cms.block config section
     * Example for yaml driver:
     * tbbc_money:
     *     currencies: ["USD", "EUR"]
     *     reference_currency: "EUR"
     *
     * @param ArrayNodeDefinition $node
     * @return void
     */
    private function addCurrencySection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('currencies')
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->scalarNode('reference_currency')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->booleanNode('enable_pair_history')
                    ->defaultValue(false)
                ->end()
                ->integerNode('decimals')
                    ->defaultValue(2)
                    ->min(0)
                ->end()
                ->scalarNode('storage')
                    ->cannotBeEmpty()
                    ->defaultValue('csv')
                    ->validate()
                    ->ifNotInArray(array('csv', 'doctrine'))
                        ->thenInvalid('Invalid storage "%s"')
                    ->end()
                ->end()
                ->scalarNode('ratio_provider')
                    ->cannotBeEmpty()
                    ->defaultValue('tbbc_money.ratio_provider.rate_exchange')
                ->end()
                ->arrayNode('templating')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('engines')
                            ->isRequired()
                            ->requiresAtLeastOneElement()
                            ->example(array('twig'))
                            ->beforeNormalization()
                                ->ifTrue(function($v){ return !is_array($v); })
                                ->then(function($v){ return array($v); })
                            ->end()
                            ->prototype('scalar')
                                ->validate()
                                    ->ifNotInArray(array('twig', 'php'))
                                    ->thenInvalid('Only "twig" and "php" engines are supported.')
                                ->end()
                            ->end()
                            ->defaultValue(array('twig'))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
