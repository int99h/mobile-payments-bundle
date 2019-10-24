<?php

namespace AnyKey\MobilePaymentsBundle\Tests\DependencyInjection;

use AnyKey\MobilePaymentsBundle\DependencyInjection\MobilePaymentsExtension;
use AnyKey\MobilePaymentsBundle\Factory\ProviderFactory;
use AnyKey\MobilePaymentsBundle\Providers\Amazon;
use AnyKey\MobilePaymentsBundle\Providers\Apple;
use AnyKey\MobilePaymentsBundle\Providers\Google;
use AnyKey\MobilePaymentsBundle\Providers\Windows;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ExtensionTest
 * @package AnyKey\MobilePaymentsBundle\Tests\DependencyInjection
 */
class MobilePaymentsExtensionTest extends TestCase
{
    /**
     *
     */
    public function testExtension()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $loader = new MobilePaymentsExtension();
        $config = $this->getConfig();
        $loader->load([$config], $container);
        // assert services
        $this->assertTrue($container->has(ProviderFactory::class));
        $this->assertTrue($container->has(Amazon::class));
        $this->assertTrue($container->has(Apple::class));
        $this->assertTrue($container->has(Google::class));
        $this->assertTrue($container->has(Windows::class));
    }

    /**
     * @return array
     */
    private function getConfig(): array
    {
        return [
            'providers' => [
                'apple_appstore' => [
                    'enabled' => true,
                    'mode' => 'sandbox',
                    'payment_key' => 'secret',
                ],
                'google_play' => [
                    'enabled' => true,
                    'package_name' => "secret",
                    'billing_key' => "secret",
                    'payment_config' => "secret",
                ],
                'amazon_appstore' => [
                    'enabled' => false,
                    'mode' => 'sandbox',
                ],
                'windows_store' => [
                    'enabled' => false,
                ],
            ],
        ];
    }
}