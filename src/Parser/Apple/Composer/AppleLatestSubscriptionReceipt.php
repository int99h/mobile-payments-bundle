<?php


namespace AnyKey\MobilePaymentsBundle\Parser\Apple\Composer;

use AnyKey\MobilePaymentsBundle\Factory\AppleSubscriptionReceiptFactory;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\AppleReceiptParserInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\SingleSubscriptionReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use ReceiptValidator\iTunes\PendingRenewalInfo;
use ReceiptValidator\iTunes\PurchaseItem;

final class AppleLatestSubscriptionReceipt implements SingleSubscriptionReceiptInterface
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
     * AppleSubscriptionReceiptComposer constructor.
     * @param AppleReceiptParserInterface $applePaymentParser
     * @param bool $isSandbox
     */
    public function __construct(AppleReceiptParserInterface $applePaymentParser, bool $isSandbox = false)
    {
        $this->purchaseItem = $applePaymentParser->parseSubscription();
        $this->pendingRenewalInfo = null;
        if ($this->purchaseItem) {
            $this->pendingRenewalInfo = $applePaymentParser->parsePendingRenewalInfo($this->purchaseItem->getProductId());
        }
        $this->refreshPayload = $applePaymentParser->parseRefreshPayload();
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

        return AppleSubscriptionReceiptFactory::createFromParsedData(
            $this->purchaseItem,
            $this->pendingRenewalInfo,
            $this->refreshPayload,
            $this->isSandbox
        );
    }
}