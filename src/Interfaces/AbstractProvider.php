<?php

namespace AnyKey\MobilePaymentsBundle\Interfaces;

use AnyKey\MobilePaymentsBundle\Exception\ConfigurationException;

/**
 * Class AbstractProvider
 * @package AnyKey\MobilePaymentsBundle\Interfaces
 */
abstract class AbstractProvider implements ProviderInterface
{
    public const MODE_SANDBOX = 'sandbox';
    public const MODE_PRODUCTION = 'production';

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