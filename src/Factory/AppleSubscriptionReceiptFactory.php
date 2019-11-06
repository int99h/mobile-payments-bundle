<?php


namespace AnyKey\MobilePaymentsBundle\Factory;


use AnyKey\MobilePaymentsBundle\Data\SubscriptionReceipt;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use ReceiptValidator\iTunes\PendingRenewalInfo;
use ReceiptValidator\iTunes\PurchaseItem;

class AppleSubscriptionReceiptFactory
{
    /**
     * Create a subscription receipt from parsed data
     *
     * @param PurchaseItem $purchaseItem
     * @param PendingRenewalInfo $pendingRenewalInfo
     * @param string $refreshPayload
     * @param bool $isSandbox
     * @return SubscriptionReceiptInterface
     */
    static public function createFromParsedData(
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