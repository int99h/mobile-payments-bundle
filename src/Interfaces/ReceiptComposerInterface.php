<?php

namespace AnyKey\Interfaces;

/**
 * Interface ReceiptComposerInterface
 * @package AnyKey\Interfaces
 */
interface ReceiptComposerInterface
{
    /**
     * Compose a purchase receipt that fits providers' validation criteria
     * @return PurchaseReceiptInterface
     */
    public function purchase(): PurchaseReceiptInterface;

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     * @return SubscriptionReceiptInterface
     */
    public function subscription(): SubscriptionReceiptInterface;
}