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
     * @param ReceiptDataInterface $receiptData
     * @return PurchaseReceiptInterface
     */
    public function validatePurchase(ReceiptDataInterface $receiptData): PurchaseReceiptInterface;

    /**
     * Validate a subscription based payment
     * @param ReceiptDataInterface $receiptData
     * @return SubscriptionReceiptInterface
     */
    public function validateSubscription(ReceiptDataInterface $receiptData): SubscriptionReceiptInterface;

    /**
     * @return string
     */
    public static function getName(): string;
}