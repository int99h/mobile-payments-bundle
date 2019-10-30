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
     * Validate a one-time purchase based payment
     *
     * @param mixed ...$config
     * @return PurchaseReceiptInterface
     */
    public function validatePurchase(...$config): PurchaseReceiptInterface;

    /**
     * Validate a subscription based payment
     * @param mixed ...$config
     * @return SubscriptionReceiptInterface
     */
    public function validateSubscription(...$config): SubscriptionReceiptInterface;

    /**
     * @return string
     */
    public static function getName(): string;
}