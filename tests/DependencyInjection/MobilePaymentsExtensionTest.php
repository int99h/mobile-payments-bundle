<?php

namespace AnyKey\Tests\DependencyInjection;

use AnyKey\DependencyInjection\MobilePaymentsExtension;
use AnyKey\Factory\ProviderFactory;
use AnyKey\Providers\Amazon;
use AnyKey\Providers\Apple;
use AnyKey\Providers\Google;
use AnyKey\Providers\Windows;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ExtensionTest
 * @package AnyKey\Tests\DependencyInjection
 */
class MobilePaymentsExtensionTest extends TestCase
{
    /**
     * Test MobilePayments Extension
     */
    public function testExtension()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $loader = new MobilePaymentsExtension();
        $loader->load([$this->getConfig()], $container);
        $container->compile();
        // are services loaded
        $this->assertTrue($container->has(ProviderFactory::class));
        $this->assertTrue($container->has(Amazon::class));
        $this->assertTrue($container->has(Apple::class));
        $this->assertTrue($container->has(Google::class));
        $this->assertTrue($container->has(Windows::class));
        // check profiders
        $factory = $container->get(ProviderFactory::class);

        if ($factory->isEnabled(Amazon::getName())) {
            $this->assertTrue(Amazon::class === get_class($factory->get(Amazon::getName())));
        }
        if ($factory->isEnabled(Apple::getName())) {
            $this->assertTrue(Apple::class === get_class($factory->get(Apple::getName())));
        }
        if ($factory->isEnabled(Google::getName())) {
            $this->assertTrue(Google::class === get_class($factory->get(Google::getName())));
        }
        if ($factory->isEnabled(Windows::getName())) {
            $this->assertTrue(Windows::class === get_class($factory->get(Windows::getName())));
        }
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
                    'payment_config' => $this->getGoogleConfig(),
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

    /**
     * @return string
     */
    private function getGoogleConfig(): string
    {
        $config = '{
          "type": "service_account",
          "project_id": "xxxxx",
          "private_key_id": "xxxxx",
          "private_key": "xxxxx",
          "client_email": "xxxxx",
          "client_id": "xxxxx",
          "auth_uri": "https://accounts.google.com/o/oauth2/auth",
          "token_uri": "https://accounts.google.com/o/oauth2/token",
          "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
          "client_x509_cert_url": "xxxxx"
        }';

        return base64_encode($config);
    }
}