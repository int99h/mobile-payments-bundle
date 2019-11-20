<?php


namespace AnyKey\MobilePaymentsBundle\Parser\Apple\Creator;

use AnyKey\MobilePaymentsBundle\Interfaces\Parser\AppleReceiptParserInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use Data\Validator\iTunes\PendingRenewalInfo;
use Data\Validator\iTunes\PurchaseItem;

class AppleSubscriptionReceiptsCreator
{
    /**
     * @var AppleReceiptParserInterface
     */
    private $appleReceiptParser;
    /**
     * @var int
     */
    private $quantity;
    /**
     * @var string
     */
    private $productId;
    /**
     * @var bool
     */
    private $isSandbox;

    /**
     * AppleSubscriptionReceiptsCreator constructor.
     * @param AppleReceiptParserInterface $appleReceiptParser
     * @param string $productId
     * @param int $quantity
     * @param bool $isSandbox
     */
    public function __construct(
        AppleReceiptParserInterface $appleReceiptParser,
        string $productId,
        int $quantity = 0,
        bool $isSandbox = false
    )
    {
        $this->appleReceiptParser = $appleReceiptParser;
        $this->quantity = $quantity;
        $this->productId = $productId;
        $this->isSandbox = $isSandbox;
    }

    /**
     * From newest to oldest
     *
     * @return SubscriptionReceiptInterface[]
     */
    public function create(): array
    {
        $subscriptionReceipts = [];

        $pendingRenewalInfo = $this->appleReceiptParser->parsePendingRenewalInfo($this->productId);

        if (!$pendingRenewalInfo) {
            return [];
        }

        foreach ($this->appleReceiptParser->parseSubscriptions($this->productId, $this->quantity) as $purchaseItem) {
            $subscriptionReceipts[] = $this->createSubscriptionReceipt($purchaseItem, $pendingRenewalInfo);
        }

        return $subscriptionReceipts;
    }

    /**
     * Generate subscription receipts from newest to oldest
     *
     * @return \Generator|null
     */
    public function generate()
    {
        $pendingRenewalInfo = $this->appleReceiptParser->parsePendingRenewalInfo($this->productId);

        if (!$pendingRenewalInfo) {
            return null;
        }

        foreach ($this->appleReceiptParser->parseSubscriptions($this->productId, $this->quantity) as $purchaseItem) {
            yield $this->createSubscriptionReceipt($purchaseItem, $pendingRenewalInfo);
        }
    }

    /**
     * @param PurchaseItem $purchaseItem
     * @param PendingRenewalInfo $pendingRenewalInfo
     * @return SubscriptionReceiptInterface|null
     */
    private function createSubscriptionReceipt(
        PurchaseItem $purchaseItem,
        PendingRenewalInfo $pendingRenewalInfo
    ): ?SubscriptionReceiptInterface
    {
        return (new AppleSubscriptionReceiptCreator(
            $purchaseItem,
            $pendingRenewalInfo,
            $this->appleReceiptParser->parseRefreshPayload(),
            $this->isSandbox
        ))->create();
    }
}