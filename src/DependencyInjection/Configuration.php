<?php

namespace AnyKey\MobilePaymentsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package AnyKey\MobilePaymentsBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('mobile_payments');
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('providers')
                    ->children()
                        ->arrayNode('amazon.appstore')
                            ->children()
                                ->enumNode('mode')
                                    ->values(['production', 'sandbox'])
                                    ->defaultValue('sandbox')
                                ->end()
                                ->scalarNode('secret')->end()
                                ->canBeEnabled()
                            ->end()
                        ->arrayNode('apple.appstore')
                            ->children()
                                ->enumNode('mode')
                                    ->values(['production', 'sandbox'])
                                    ->defaultValue('sandbox')
                                ->end()
                                ->scalarNode('payment_key')->end()
                                ->canBeDisabled()
                            ->end()
                        ->end()
                        ->arrayNode('google.play')
                            ->children()
                                ->scalarNode('package_name')->end()
                                ->scalarNode('billing_key')->end()
                                ->scalarNode('payment_config')->end()
                                ->canBeDisabled()
                            ->end()
                        ->end()
                        ->arrayNode('windows.store')
                            ->children()
                                ->variableNode('cache')->end()
                                ->canBeEnabled()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}