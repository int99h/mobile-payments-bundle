<?php

namespace AnyKey\MobilePaymentsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Contracts\Cache\CacheInterface;
use AnyKey\MobilePaymentsBundle\Factory\ProviderFactory;
use AnyKey\MobilePaymentsBundle\Providers;

/**
 * Class MobilePaymentsExtension
 * @package AnyKey\MobilePaymentsBundle\DependencyInjection
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
        ;
        // Amazon
        $container->getDefinition(Providers\Amazon::class)
            ->setPublic(true)
            ->setAutowired(true)
            ->setArgument('$enabled', $amazon['enabled'])
            ->setArgument('$mode', $amazon['mode'])
            ->setArgument('$secret', $amazon['secret'] ?? null)
        ;
        // Google
        $container->getDefinition(Providers\Google::class)
            ->setPublic(true)
            ->setAutowired(true)
            ->setArgument('$enabled', $google['enabled'])
            ->setArgument('$packageName', $google['package_name'])
            ->setArgument('$billingKey', $google['billing_key'])
            ->setArgument('$paymentConfig', $google['payment_config'])
        ;
        // Windows
        $container->getDefinition(Providers\Windows::class)
            ->setPublic(true)
            ->setAutowired(true)
            ->setArgument('$enabled', $windows['enabled'])
        ;
        if ($windows['cache'] && $windows['cache'] instanceof CacheInterface) {
            $container->getDefinition(Providers\Windows::class)
                ->setArgument('$cache', $windows['cache'] ?? null)
            ;
        }

        /** Factory */
        $container->getDefinition(ProviderFactory::class)
            ->setPublic(true)
            ->setAutowired(true)
            ->setArgument('$providers', $config['providers'])
        ;
    }
}