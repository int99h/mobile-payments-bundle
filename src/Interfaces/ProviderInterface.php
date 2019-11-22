<?php

namespace AnyKey\Interfaces;

/**
 * Interface ProviderInterface
 * @package AnyKey\Interfaces
 */
interface ProviderInterface
{
    public const TAG = 'mobile_payment.provider';

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * Retrieve the original response from the payment provider
     * @return mixed
     */
    public function getResponse();

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