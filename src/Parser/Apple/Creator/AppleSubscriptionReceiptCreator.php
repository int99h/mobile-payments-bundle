<?php


namespace AnyKey\MobilePaymentsBundle\Parser\Apple\Creator;

use AnyKey\MobilePaymentsBundle\Factory\AppleReceiptFactory;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use ReceiptValidator\iTunes\PendingRenewalInfo;
use ReceiptValidator\iTunes\PurchaseItem;

class AppleSubscriptionReceiptCreator
{
    /**
     * @var PurchaseItem|null
     */
    private $purchaseItem;
    /**
     * @var PendingRenewalInfo|null
     */
    private $pendingRenewalInfo;
    /**
     * @var string
     */
    private $refreshPayload;
    /**
     * @var bool
     */
    private $isSandbox;

    /**
     * AppleSubscriptionReceiptCreator constructor.
     * @param PurchaseItem $purchaseItem
     * @param PendingRenewalInfo $pendingRenewalInfo
     * @param string $refreshPayload
     * @param bool $isSandbox
     */
    public function __construct(
        PurchaseItem $purchaseItem,
        PendingRenewalInfo $pendingRenewalInfo,
        string $refreshPayload,
        bool $isSandbox = false
    )
    {
        $this->purchaseItem = $purchaseItem;
        $this->pendingRenewalInfo = $pendingRenewalInfo;
        $this->refreshPayload = $refreshPayload;
        $this->isSandbox = $isSandbox;
    }

    /**
     * @return SubscriptionReceiptInterface|null
     */
    public function create(): ?SubscriptionReceiptInterface
    {
        return AppleReceiptFactory::createSubscriptionFromParsedData(
            $this->purchaseItem,
            $this->pendingRenewalInfo,
            $this->refreshPayload,
            $this->isSandbox
        );
    }
}