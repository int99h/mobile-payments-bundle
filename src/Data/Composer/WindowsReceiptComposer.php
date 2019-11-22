<?php


namespace AnyKey\Data\Composer;

use AnyKey\Interfaces\PurchaseReceiptInterface;
use AnyKey\Interfaces\ReceiptComposerInterface;
use AnyKey\Interfaces\ReceiptDataInterface;
use AnyKey\Interfaces\SubscriptionReceiptInterface;
use AnyKey\Data\PurchaseReceipt;
use AnyKey\Data\SubscriptionReceipt;

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