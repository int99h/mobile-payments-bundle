<?php

namespace AnyKey\Interfaces;

/**
 * Interface SubscriptionReceiptInterface
 * @package AnyKey\Interfaces
 */
interface SubscriptionReceiptInterface extends PurchaseReceiptInterface
{
    /**
     * Check if receipt is expired
     * @return bool
     */
    public function isExpired(): bool;

    /**
     * Get expiry date of the payment
     * @return \DateTime
     */
    public function getExpiryDate(): \DateTime;

    /**
     * Is payment renewing
     * @return bool
     */
    public function isRenewing(): bool;

    /**
     * Is payment receipt trial
     * @return bool
     */
    public function isTrial(): bool;
}