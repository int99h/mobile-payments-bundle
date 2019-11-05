<?php


namespace AnyKey\MobilePaymentsBundle\Data\Composer;

use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\ReceiptComposerInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\ReceiptDataInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use AnyKey\MobilePaymentsBundle\Data\PurchaseReceipt;
use AnyKey\MobilePaymentsBundle\Data\SubscriptionReceipt;

class WindowsReceiptComposer implements ReceiptComposerInterface
{
    /**
     * @var ReceiptDataInterface
     */
    private $receiptData;

    /**
     * WindowsReceiptComposer constructor.
     * @param ReceiptDataInterface $receiptData
     */
    public function __construct(ReceiptDataInterface $receiptData)
    {
        $this->receiptData = $receiptData;
    }

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     * @return PurchaseReceiptInterface
     */
    public function purchase(): PurchaseReceiptInterface
    {
        return new PurchaseReceipt();
    }

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     * @return SubscriptionReceiptInterface
     */
    public function subscription(): SubscriptionReceiptInterface
    {
        return new SubscriptionReceipt();
    }
}