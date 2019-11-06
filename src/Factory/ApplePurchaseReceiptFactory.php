<?php


namespace AnyKey\MobilePaymentsBundle\Factory;


use AnyKey\MobilePaymentsBundle\Data\PurchaseReceipt;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
use ReceiptValidator\iTunes\PurchaseItem;

class ApplePurchaseReceiptFactory
{
    /**
     * Create a purchase receipt from parsed data
     *
     * @param PurchaseItem $purchaseItem
     * @param string $refreshPayload
     * @param bool $isSandbox
     * @return PurchaseReceiptInterface
     */
    static public function createFromParsedData(
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
}