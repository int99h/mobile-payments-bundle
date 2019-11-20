<?php


namespace AnyKey\MobilePaymentsBundle\Parser\Apple\Creator;

use AnyKey\MobilePaymentsBundle\Factory\AppleReceiptFactory;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\AppleReceiptParserInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use Data\Validator\iTunes\PendingRenewalInfo;
use Data\Validator\iTunes\PurchaseItem;

class AppleLatestSubscriptionReceiptCreator
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
     * AppleLatestSubscriptionReceipt constructor.
     * @param AppleReceiptParserInterface $appleReceiptParser
     * @param bool $isSandbox
     */
    public function __construct(AppleReceiptParserInterface $appleReceiptParser, bool $isSandbox = false)
    {
        $this->purchaseItem = $appleReceiptParser->parseSubscription();
        $this->pendingRenewalInfo = null;
        if ($this->purchaseItem) {
            $this->pendingRenewalInfo = $appleReceiptParser->parsePendingRenewalInfo($this->purchaseItem->getProductId());
        }
        $this->refreshPayload = $appleReceiptParser->parseRefreshPayload();
        $this->isSandbox = $isSandbox;
    }

    /**
     * @return PurchaseItem|null
     */
    public function getSubscriptionPurchaseItem(): ?PurchaseItem
    {
        return $this->purchaseItem;
    }

    /**
     * @return PendingRenewalInfo|null
     */
    public function getPendingRenewalInfo(): ?PendingRenewalInfo
    {
        return $this->pendingRenewalInfo;
    }

    /**
     * @return string|null
     */
    public function getRefreshPayload(): ?string
    {
        return $this->refreshPayload;
    }

    /**
     * @return SubscriptionReceiptInterface|null
     */
    public function create(): ?SubscriptionReceiptInterface
    {
        if (!$this->purchaseItem || !$this->pendingRenewalInfo) {
            return null;
        }

        return AppleReceiptFactory::createSubscriptionFromParsedData(
            $this->purchaseItem,
            $this->pendingRenewalInfo,
            $this->refreshPayload,
            $this->isSandbox
        );
    }
}