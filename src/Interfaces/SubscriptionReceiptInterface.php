<?php


namespace AnyKey\MobilePaymentsBundle\Interfaces;


interface SubscriptionReceiptInterface extends PurchaseReceiptInterface
{
    /**
     * Get expiry date of the payment
     *
     * @return \DateTime
     */
    public function getExpiryDate(): \DateTime;

    /**
     * Is payment renewing
     *
     * @return bool
     */
    public function isRenewing(): bool;

    /**
     * Is payment receipt trial
     *
     * @return bool
     */
    public function isTrial(): bool;
}