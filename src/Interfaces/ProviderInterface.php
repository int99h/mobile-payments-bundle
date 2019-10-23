<?php

namespace AnyKey\MobilePaymentsBundle\Interfaces;

/**
 * Interface ProviderInterface
 * @package AnyKey\MobilePaymentsBundle\Interfaces
 */
interface ProviderInterface
{
    public const MODE_SANDBOX = 'sandbox';
    public const MODE_PRODUCTION = 'production';

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @return string
     */
    public function getAlias(): string;
}