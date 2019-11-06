<?php


namespace AnyKey\MobilePaymentsBundle\Parser\Apple\Composer;

use AnyKey\MobilePaymentsBundle\Interfaces\Parser\AppleReceiptParserInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\MultipleSubscriptionReceiptsInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\ReceiptsGeneratorInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use ReceiptValidator\iTunes\PendingRenewalInfo;
use ReceiptValidator\iTunes\PurchaseItem;

final class AppleSubscriptionReceiptsCollection implements MultipleSubscriptionReceiptsInterface, ReceiptsGeneratorInterface
{
    /**
     * @var AppleReceiptParserInterface
     */
    private $applePaymentParser;
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
     * AppleSubscriptionReceiptsCollection constructor.
     * @param AppleReceiptParserInterface $applePaymentParser
     * @param string $productId
     * @param int $quantity - if set to 0, all subscription items are rendered/generated
     * @param bool $isSandbox
     */
    public function __construct(
        AppleReceiptParserInterface $applePaymentParser,
        string $productId,
        int $quantity = 0,
        bool $isSandbox = false
    )
    {
        $this->applePaymentParser = $applePaymentParser;
        $this->quantity = $quantity;
        $this->productId = $productId;
        $this->isSandbox = $isSandbox;
    }

    /**
     * From newest to oldest
     *
     * @return SubscriptionReceiptInterface[]
     */
    public function render(): array
    {
        $subscriptionProductReceipts = [];

        $pendingRenewalInfo = $this->applePaymentParser->parsePendingRenewalInfo($this->productId);

        if (!$pendingRenewalInfo) {
            return [];
        }

        foreach ($this->applePaymentParser->parseSubscriptions($this->productId, $this->quantity) as $purchaseItem) {
            $subscriptionProductReceipts[] =
                $this->createSubscriptionReceipt($purchaseItem, $pendingRenewalInfo);
        }

        return $subscriptionProductReceipts;
    }

    /**
     * Generate subscription receipts from newest to oldest
     *
     * @return \Generator|null
     */
    public function generate()
    {
        $pendingRenewalInfo = $this->applePaymentParser->parsePendingRenewalInfo($this->productId);

        if (!$pendingRenewalInfo) {
            return null;
        }

        foreach ($this->applePaymentParser->parseSubscriptions($this->productId, $this->quantity) as $purchaseItem) {
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
        return (new AppleSubscriptionReceiptComposer(
            $purchaseItem,
            $pendingRenewalInfo,
            $this->applePaymentParser->parseRefreshPayload(),
            $this->isSandbox
        ))->create();
    }
}