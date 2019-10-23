<?php

namespace AnyKey\MobilePaymentsBundle\Providers;

use AnyKey\MobilePaymentsBundle\Exception\ConfigurationException;
use AnyKey\MobilePaymentsBundle\Interfaces\ProviderInterface;

/**
 * Class AbstractProvider
 * @package AnyKey\MobilePaymentsBundle\Providers
 */
abstract class AbstractProvider implements ProviderInterface
{
    /** @var bool */
    protected $enabled;
    private $validator;

    abstract protected function initValidator(): void;

    /**
     * @param bool $enabled
     * @return AbstractProvider
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @throws ConfigurationException
     */
    protected function checkAvailability(): void
    {
        if (!$this->isEnabled()) {
            throw new ConfigurationException($this,'provider not enabled');
        }
        if (!$this->validator) {
            throw new ConfigurationException($this, 'validator not initialized');
        }
    }
}