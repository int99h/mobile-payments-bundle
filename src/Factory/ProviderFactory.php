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
     * @param array $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
        foreach ($providers as $provider) {
            if ($provider instanceof ProviderInterface && $provider->isEnabled()) {
                $this->enabled[$provider->getAlias()] = $provider;
            }
        }
    }

    /**
     * @param string $alias
     * @return ProviderInterface
     * @throws ConfigurationException
     * @throws RuntimeException
     */
    public function get(string $alias): ProviderInterface
    {
        if (array_key_exists($alias, $this->enabled)) {
            return $this->enabled[$alias];
        }
        if (array_key_exists($alias, $this->providers)) {
            $provider = $this->providers[$alias];
            throw new ConfigurationException($provider, 'provider not enabled');
        }
        throw new RuntimeException("{$alias} provider not exist");
    }
}