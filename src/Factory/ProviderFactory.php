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
    public function getAvailable(): array
    {
        return array_keys($this->providers);
    }

    /**
     * @return array
     */
    public function getEnabled(): array
    {
        return array_keys($this->enabled);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isExist(string $name): bool
    {
        return array_key_exists($name, $this->providers);
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
     * @example get(Provider::class) or get(Provider::getName())
     * @return ProviderInterface
     * @throws ConfigurationException
     * @throws RuntimeException
     */
    public function get(string $name): ProviderInterface
    {
        // validate
        if (empty($name)) {
            throw new RuntimeException("Name not defined!");
        }
        // try class
        $provider = $this->getByClassName($name);
        // try alias
        if (is_null($provider)) {
            $provider = $this->getByAlias($name);
        }
        if (is_null($provider)) {
            $available = implode(', ', $this->getAvailable());
            throw new RuntimeException("{$name} provider not exist. Available providers: {$available}");
        }

        return $provider;
    }

    /**
     * @param string $name
     * @return ProviderInterface|null
     * @throws ConfigurationException
     */
    private function getByClassName(string $name): ?ProviderInterface
    {
        $result = null;
        if (class_exists($name)) {
            // try to get from available
            foreach ($this->providers as $provider) {
                if (get_class($provider) === $name) {
                    $result = $provider;
                    break;
                }
            }
            // check enabled
            if ($result instanceof ProviderInterface && !$result->isEnabled()) {
                throw new ConfigurationException($result, 'provider not enabled');
            }
        }

        return $result;
    }

    /**
     * @param string $name
     * @return ProviderInterface|null
     * @throws ConfigurationException
     */
    private function getByAlias(string $name): ?ProviderInterface
    {
        $result = null;
        // try to get from available
        if (array_key_exists($name, $this->providers)) {
            $result = $this->providers[$name];
        }
        // check enabled
        if ($result instanceof ProviderInterface && !$result->isEnabled()) {
            throw new ConfigurationException($result, 'provider not enabled');
        }

        return $result;
    }
}