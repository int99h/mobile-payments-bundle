<?php

namespace AnyKey\MobilePaymentsBundle\Tests\DependencyInjection;

use AnyKey\MobilePaymentsBundle\DependencyInjection\Configuration;
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
     * @dataProvider dataTestConfiguration
     * @param mixed $inputConfig
     * @param mixed $expectedConfig
     */
    public function testConfiguration($inputConfig, $expectedConfig)
    {
        $configuration = new Configuration();

        $tree = $configuration->getConfigTreeBuilder();
        $this->assertTrue($tree instanceof TreeBuilder);
        $node = $tree->buildTree();
        $this->assertTrue($node instanceof NodeInterface);
        $normalizedConfig = $node->normalize($inputConfig);
        $finalizedConfig = $node->finalize($normalizedConfig);

        $this->assertEquals($expectedConfig, $finalizedConfig);
    }

    /**
     * @return array
     */
    public function dataTestConfiguration()
    {
        return [
            'test configuration' => [
                $this->getInputConfig(),
                $this->getExpectedConfig(),
            ],
        ];
    }

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
                    'payment_config' => "secret",
                ],
            ],
        ];
    }

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