<?php

namespace AnyKey\MobilePaymentsBundle\Interfaces;

/**
 * Interface ProviderInterface
 * @package AnyKey\MobilePaymentsBundle\Interfaces
 */
interface ProviderInterface
{
    public const TAG = 'mobile_payment.provider';

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @return string
     */
    public static function getName(): string;
}