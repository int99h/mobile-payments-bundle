<?php

namespace AnyKey\MobilePaymentsBundle\Interfaces;

/**
 * Interface ReceiptComposerInterface
 * @package AnyKey\MobilePaymentsBundle\Interfaces
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