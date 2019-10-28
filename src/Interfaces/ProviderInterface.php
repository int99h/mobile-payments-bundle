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
     * @param mixed ...$config
     * @return mixed
     */
    public function validate(...$config);

    /**
     * @return string
     */
    public static function getName(): string;
}