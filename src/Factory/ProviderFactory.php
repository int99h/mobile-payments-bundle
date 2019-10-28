<?php

namespace AnyKey\MobilePaymentsBundle\Factory;

use AnyKey\MobilePaymentsBundle\Exception\ConfigurationException;
use AnyKey\MobilePaymentsBundle\Exception\RuntimeException;
use AnyKey\MobilePaymentsBundle\Interfaces\ProviderInterface;

/**
 * Class ProviderFactory
 * @package AnyKey\MobilePaymentsBundle\Factory
 */
class ProviderFactory
{
    /** @var array */
    private $providers = [];
    /** @var array */
    private $enabled = [];

    /**
     * ProviderFactory constructor.
     * @param iterable $providers
     */
    public function __construct(iterable $providers)
    {
        foreach ($providers as $provider) {
            if ($provider instanceof ProviderInterface) {
                $this->providers[$provider::getName()] = $provider;
                if ($provider->isEnabled()) {
                    $this->enabled[$provider::getName()] = $provider;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getEnabled(): array
    {
        return $this->enabled;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isEnabled(string $name): bool
    {
        return array_key_exists($name, $this->enabled);
    }

    /**
     * @param string $name
     * @return ProviderInterface
     * @throws ConfigurationException
     * @throws RuntimeException
     */
    public function get(string $name): ProviderInterface
    {
        if (array_key_exists($name, $this->enabled)) {
            return $this->enabled[$name];
        }
        if (array_key_exists($name, $this->providers)) {
            $provider = $this->providers[$name];
            throw new ConfigurationException($provider, 'provider not enabled');
        }
        var_dump($this->providers[$name]);exit;
        throw new RuntimeException("{$name} provider not exist");
    }
}