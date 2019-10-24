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
                        ->arrayNode('amazon_appstore')->normalizeKeys(false)
                            ->canBeEnabled()
                            ->children()
                                ->enumNode('mode')->values(['production', 'sandbox'])->defaultValue('sandbox')->end()
                                ->scalarNode('secret')->end()
                            ->end()
                        ->end()
                        ->arrayNode('apple_appstore')->normalizeKeys(false)
                            ->canBeDisabled()
                            ->children()
                                ->enumNode('mode')->values(['production', 'sandbox'])->defaultValue('sandbox')->end()
                                ->scalarNode('payment_key')->end()
                            ->end()
                        ->end()
                        ->arrayNode('google_play')->normalizeKeys(false)
                            ->canBeDisabled()
                            ->children()
                                ->scalarNode('package_name')->end()
                                ->scalarNode('billing_key')->end()
                                ->scalarNode('payment_config')->end()
                            ->end()
                        ->end()
                        ->arrayNode('windows_store')->normalizeKeys(false)
                            ->canBeEnabled()
                            ->children()
                                ->variableNode('cache')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}