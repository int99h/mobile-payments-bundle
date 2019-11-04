<?php


namespace AnyKey\MobilePaymentsBundle\Data\Composer;

use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\ReceiptComposerInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use AnyKey\MobilePaymentsBundle\Model\PurchaseReceipt;
use AnyKey\MobilePaymentsBundle\Model\SubscriptionReceipt;

class WindowsReceiptComposer implements ReceiptComposerInterface
{
    /**
     * @var array
     */
    private $receipt;

    /**
     * WindowsReceiptComposer constructor.
     * @param array $receipt
     */
    public function __construct(array $receipt)
    {
        $this->receipt = $receipt;
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