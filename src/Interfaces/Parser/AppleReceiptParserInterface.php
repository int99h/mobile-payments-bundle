<?php


namespace AnyKey\Interfaces\Parser;

use AnyKey\Data\Validator\iTunes\PendingRenewalInfo;
use AnyKey\Data\Validator\iTunes\PurchaseItem;

interface AppleReceiptParserInterface
{
    /**
     * Parse subscription renewal info by product ID
     * @param string $productId
     * @return PendingRenewalInfo|null
     */
    public function parsePendingRenewalInfo(string $productId): ?PendingRenewalInfo;

    /**
     * Retrieve the latest subscription from an Apple receipt
     * @return PurchaseItem|null
     */
    public function parseSubscription(): ?PurchaseItem;

    /**
     * Parse subscriptions by product ID from an Apple receipt
     * @param string $productId
     * @param int $quantity
     * @return \Generator
     */
    public function parseSubscriptions(string $productId, $quantity = 1);

    /**
     * Parse purchases from an Apple receipt
     * @return PurchaseItem[]
     */
    public function parsePurchases(): array;

    /**
     * Parse refresh payload that is required to request updates on the latest receipt
     * @return string
     */
    public function parseRefreshPayload(): string;
}