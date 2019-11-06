<?php


namespace AnyKey\MobilePaymentsBundle\Interfaces\Parser;

use ReceiptValidator\iTunes\PendingRenewalInfo;
use ReceiptValidator\iTunes\PurchaseItem;

interface AppleReceiptParserInterface
{
    /**
     * @param string $productId
     * @return PendingRenewalInfo|null
     */
    public function parsePendingRenewalInfo(string $productId): ?PendingRenewalInfo;

    /**
     * @return PurchaseItem|null
     */
    public function parseSubscription(): ?PurchaseItem;

    /**
     * Parse subscriptions of the same kind from the receipt
     *
     * @param string $productId
     * @param int $quantity
     * @return \Generator
     */
    public function parseSubscriptions(string $productId, $quantity = 1);

    /**
     * @return PurchaseItem[]
     */
    public function parsePurchases(): array;

    /**
     * @return string
     */
    public function parseRefreshPayload(): string;
}