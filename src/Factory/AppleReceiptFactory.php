<?php


namespace AnyKey\MobilePaymentsBundle\Factory;


use AnyKey\MobilePaymentsBundle\Data\PurchaseReceipt;
use AnyKey\MobilePaymentsBundle\Data\SubscriptionReceipt;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use AnyKey\MobilePaymentsBundle\Data\Validator\iTunes\PendingRenewalInfo;
use AnyKey\MobilePaymentsBundle\Data\Validator\iTunes\PurchaseItem;

class AppleReceiptFactory
{
    /**
     * Create a purchase receipt from parsed data
     *
     * @param PurchaseItem $purchaseItem
     * @param string $refreshPayload
     * @param bool $isSandbox
     * @return PurchaseReceiptInterface
     */
    static public function createPurchaseFromParsedData(
        PurchaseItem $purchaseItem,
        string $refreshPayload,
        bool $isSandbox
    ): PurchaseReceiptInterface
    {
        $receipt = (new PurchaseReceipt())
            ->setProductId($purchaseItem->getProductId())
            ->setTransactionId($purchaseItem->getTransactionId())
            ->setOrderId($purchaseItem->getOriginalTransactionId())
            ->setRefreshPayload($refreshPayload)
            ->setSandbox($isSandbox)
            ->setRawResponse(json_encode($purchaseItem->getRawResponse()))
        ;

        $rawResponse = $purchaseItem->getRawResponse();
        if ($rawResponse) {
            $receipt->setRawResponse(json_encode($rawResponse));
        }

        return $receipt;
    }

    /**
     * Create a subscription receipt from parsed data
     *
     * @param PurchaseItem $purchaseItem
     * @param PendingRenewalInfo $pendingRenewalInfo
     * @param string $refreshPayload
     * @param bool $isSandbox
     * @return SubscriptionReceiptInterface
     */
    static public function createSubscriptionFromParsedData(
        PurchaseItem $purchaseItem,
        PendingRenewalInfo $pendingRenewalInfo,
        string $refreshPayload,
        bool $isSandbox
    ): SubscriptionReceiptInterface
    {
        $receipt = (new SubscriptionReceipt())
            ->setExpiryDate($purchaseItem->getExpiresDate()->toDateTime())
            ->setProductId($purchaseItem->getProductId())
            ->setTransactionId($purchaseItem->getTransactionId())
            ->setOrderId($purchaseItem->getOriginalTransactionId())
            ->setRenewing($pendingRenewalInfo->getAutoRenewStatus())
            ->setRefreshPayload($refreshPayload)
            ->setTrial($purchaseItem->isTrialPeriod())
            ->setSandbox($isSandbox)
        ;

        $rawResponse = $purchaseItem->getRawResponse();
        if ($rawResponse) {
            $receipt->setRawResponse(json_encode($rawResponse));
        }

        return $receipt;
    }
}