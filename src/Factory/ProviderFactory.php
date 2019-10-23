<?php

namespace AnyKey\MobilePaymentsBundle\Factory;

use AnyKey\MobilePaymentsBundle\Exception\ProviderException;
use AnyKey\MobilePaymentsBundle\Interfaces\ProviderInterface;

/**
 * Class ProviderFactory
 * @package AnyKey\MobilePaymentsBundle\Factory
 */
class ProviderFactory
{
    private $providers = [];
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
     * @throws ProviderException
     */
    public function get(string $alias): ProviderInterface
    {
        if (array_key_exists($alias, $this->enabled)) {
            return $this->enabled[$alias];
        }
        if (array_key_exists($alias, $this->providers)) {
            throw new ProviderException("{$alias} provider not enabled");
        }
        throw new ProviderException("{$alias} provider not exist");
    }
}