<?php


namespace AnyKey\MobilePaymentsBundle\Composer\PaymentReceipt;


use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;

interface PaymentReceiptComposerInterface
{
    /**
     * Compose a purchase receipt that fits providers' validation criteria
     *
     * @return PurchaseReceiptInterface
     */
    public function composePurchase(): PurchaseReceiptInterface;

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     *
     * @return SubscriptionReceiptInterface
     */
    public function composeSubscription(): SubscriptionReceiptInterface;
}