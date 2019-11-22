<?php

namespace AnyKey\DependencyInjection;

use AnyKey\Interfaces\ProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Contracts\Cache\CacheInterface;
use AnyKey\Factory\ProviderFactory;
use AnyKey\Providers;

/**
 * Class MobilePaymentsExtension
 * @package AnyKey\DependencyInjection
 */
class MobilePaymentsExtension extends Extension
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $apple = $config['providers']['apple_appstore'];
        $amazon = $config['providers']['amazon_appstore'];
        $google = $config['providers']['google_play'];
        $windows = $config['providers']['windows_store'];

        /** Providers */
        // Apple
        $container->getDefinition(Providers\Apple::class)
            ->setPublic(true)
            ->setAutowired(true)
            ->setArgument('$enabled', $apple['enabled'])
            ->setArgument('$mode', $apple['mode'])
            ->setArgument('$paymentKey', $apple['payment_key'])
            ->addTag(ProviderInterface::TAG, ['alias' => Providers\Apple::getName()])
        ;
        // Amazon
        $container->getDefinition(Providers\Amazon::class)
            ->setPublic(true)
            ->setAutowired(true)
            ->setArgument('$enabled', $amazon['enabled'])
            ->setArgument('$mode', $amazon['mode'])
            ->setArgument('$secret', $amazon['secret'] ?? null)
            ->addTag(ProviderInterface::TAG, ['alias' => Providers\Amazon::getName()])
        ;
        // Google
        $container->getDefinition(Providers\Google::class)
            ->setPublic(true)
            ->setAutowired(true)
            ->setArgument('$enabled', $google['enabled'])
            ->setArgument('$packageName', $google['package_name'])
            ->setArgument('$billingKey', $google['billing_key'])
            ->setArgument('$paymentConfig', $google['payment_config'])
            ->addTag(ProviderInterface::TAG, ['alias' => Providers\Google::getName()])
        ;
        // Windows
        $container->getDefinition(Providers\Windows::class)
            ->setPublic(true)
            ->setAutowired(true)
            ->setArgument('$enabled', $windows['enabled'])
            ->addTag(ProviderInterface::TAG, ['alias' => Providers\Windows::getName()])
        ;
        if ($windows['cache'] && $windows['cache'] instanceof CacheInterface) {
            $container->getDefinition(Providers\Windows::class)
                ->setArgument('$cache', $windows['cache'] ?? null)
            ;
        }

        /** Factory */
        $container->getDefinition(ProviderFactory::class)
            ->setPublic(true)
            ->setAutoconfigured(true)
            ->setAutowired(true)
        ;
    }
}