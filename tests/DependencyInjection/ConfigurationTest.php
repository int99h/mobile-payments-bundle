<?php

namespace AnyKey\Tests\DependencyInjection;

use AnyKey\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\NodeInterface;

/**
 * Class ConfigurationTest
 * @package DependencyInjection
 */
class ConfigurationTest extends TestCase
{
    /**
     * Test DI Configuration
     */
    public function testConfiguration()
    {
        $configuration = new Configuration();

        $tree = $configuration->getConfigTreeBuilder();
        $this->assertTrue($tree instanceof TreeBuilder);
        $node = $tree->buildTree();
        $this->assertTrue($node instanceof NodeInterface);
        $normalizedConfig = $node->normalize($this->getInputConfig());
        $finalizedConfig = $node->finalize($normalizedConfig);

        $this->assertEquals($this->getExpectedConfig(), $finalizedConfig);
    }

    /**
     * @return array
     */
    private function getInputConfig(): array
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
            ],
        ];
    }

    /**
     * @return array
     */
    private function getExpectedConfig(): array
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
                    'secret' => null,
                ],
                'windows_store' => [
                    'enabled' => false,
                    'cache' => null,
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