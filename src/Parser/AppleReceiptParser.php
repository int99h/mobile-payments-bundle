<?php

namespace AnyKey\MobilePaymentsBundle\Parser;

use AnyKey\MobilePaymentsBundle\Interfaces\Parser\AppleReceiptParserInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\PurchaseItemGeneratorInterface;
use AnyKey\MobilePaymentsBundle\Parser\Apple\PurchaseItemGenerator;
use ReceiptValidator\iTunes\PendingRenewalInfo;
use ReceiptValidator\iTunes\PurchaseItem;
use ReceiptValidator\iTunes\ResponseInterface;

class AppleReceiptParser implements AppleReceiptParserInterface
{
    /**
     * @var ResponseInterface
     */
    private $response;
    /**
     * @var PurchaseItemGeneratorInterface
     */
    private $purchaseItemGenerator;

    /**
     * AppleReceiptParser constructor.
     * @param ResponseInterface $response
     * @param PurchaseItemGeneratorInterface|null $purchaseItemGenerator
     */
    public function __construct(
        ResponseInterface $response,
        PurchaseItemGeneratorInterface $purchaseItemGenerator = null
    )
    {
        $this->response = $response;
        if ($this->response->getResultCode() == ResponseInterface::RESULT_SHARED_SECRET_NOT_MATCH) {
            throw new \Error('Invalid Apple receipt.');
        }

        $rawResponse = $this->getRawResponse();

        if (!$purchaseItemGenerator) {
            $this->purchaseItemGenerator = new PurchaseItemGenerator();
        } else {
            $this->purchaseItemGenerator = $purchaseItemGenerator;
        }

        $this->purchaseItemGenerator->init($rawResponse);
    }

    /**
     * @return string
     */
    private function getRawResponse(): string
    {
        $rawResponse = $this->response->getRawData();
        if (!$rawResponse) {
            throw new \Error('Apple receipt response cannot be empty.');
        }

        return json_encode($rawResponse);
    }


    /**
     * Parse pending renewal info for subscription purchase item.
     *
     * @param string $productId
     * @return PendingRenewalInfo|null
     */
    public function parsePendingRenewalInfo(string $productId): ?PendingRenewalInfo
    {
        foreach ($this->response->getPendingRenewalInfo() as $pendingRenewalInfo) {
            if ($pendingRenewalInfo->getProductId() == $productId) {
                return $pendingRenewalInfo;
            }
        }

        return null;
    }

    /**
     * Retrieve subscription purchase item.
     *
     * @return PurchaseItem|null
     */
    public function parseSubscription(): ?PurchaseItem
    {
        /** @var PurchaseItem $purchaseItem */
        foreach ($this->purchaseItemGenerator->generateSubscriptionPurchaseItems() as $purchaseItem) {
            return $purchaseItem;
        }

        return null;
    }

    /**
     * @return PurchaseItem[]
     */
    public function parsePurchaseProducts(): array
    {
        $purchaseProducts = [];

        /** @var PurchaseItem $purchaseItem */
        foreach ($this->purchaseItemGenerator->generateProductPurchaseItems() as $purchaseItem) {
            $purchaseProducts[$purchaseItem->getProductId()] = $purchaseItem;
        }

        return $purchaseProducts;
    }

    /**
     * @return string
     */
    public function parseRefreshPayload(): string
    {
        return $this->response->getLatestReceipt();
    }

    /**
     * Parse subscription transactions by subscription product ID from the receipt
     *
     * @param string $productId subscription product ID to look for
     * @param int $quantity     (if set to 0, all items are being parsed)
     * @return \Generator       returns a PurchaseItem
     */
    public function parseSubscriptions(string $productId, $quantity = 0)
    {
        $subscriptionsCount = 0;

        /** @var PurchaseItem $purchaseItem */
        foreach ($this->purchaseItemGenerator->generateSubscriptionPurchaseItems() as $purchaseItem) {
            if ($productId !== $purchaseItem->getProductId()) {
                continue;
            }

            if ($quantity !== 0 && $subscriptionsCount >= $quantity) {
                break;
            }

            ++$subscriptionsCount;
            yield $purchaseItem;
        }
    }
}
