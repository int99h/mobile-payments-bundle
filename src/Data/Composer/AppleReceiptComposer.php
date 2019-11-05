<?php

namespace AnyKey\MobilePaymentsBundle\Data\Composer;

use AnyKey\MobilePaymentsBundle\Exception\RuntimeException;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\ReceiptComposerInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use AnyKey\MobilePaymentsBundle\Data\PurchaseReceipt;
use AnyKey\MobilePaymentsBundle\Data\SubscriptionReceipt;
use ReceiptValidator\iTunes\PendingRenewalInfo;
use ReceiptValidator\iTunes\ResponseInterface;

/**
 * Class AppleReceiptComposer
 * @package AnyKey\MobilePaymentsBundle\Data\Composer
 */
class AppleReceiptComposer implements ReceiptComposerInterface
{
    /** @var ResponseInterface */
    private $response;

    /**
     * AppleReceiptComposer constructor.
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     * @return PurchaseReceiptInterface
     */
    public function purchase(): PurchaseReceiptInterface
    {
        $latestReceipt = $this->response->getLatestReceiptInfo()[0];
        $receipt = (new PurchaseReceipt())
            ->setProductId($latestReceipt->getProductId())
            ->setTransactionId($latestReceipt->getTransactionId())
            ->setOrderId($latestReceipt->getOriginalTransactionId())
            ->setRefreshPayload($this->response->getLatestReceipt())
            ->setSandbox($this->response->isSandbox())
            ->setRawResponse(json_encode($this->response->getRawData()))
            ->setOriginalResponse($this->response)
        ;

        return $receipt;
    }

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     * @return SubscriptionReceiptInterface
     * @throws RuntimeException
     */
    public function subscription(): SubscriptionReceiptInterface
    {
        $latestReceipt = $this->response->getLatestReceiptInfo()[0];
        if (!$latestReceipt) {
            throw new RuntimeException('Cannot retrieve the latest receipt.');
        }

        $data = $this->getSubscriptionData($latestReceipt->getProductId());
        $subscription = (new SubscriptionReceipt())
            ->setExpiryDate($latestReceipt->getExpiresDate()->toDateTime())
            ->setProductId($latestReceipt->getProductId())
            ->setTransactionId($latestReceipt->getTransactionId())
            ->setOrderId($latestReceipt->getOriginalTransactionId())
            ->setRenewing($data ? $data->getAutoRenewStatus() : null)
            ->setRefreshPayload($this->response->getLatestReceipt())
            ->setTrial($latestReceipt->isTrialPeriod())
            ->setSandbox($this->response->isSandbox())
            ->setRawResponse(json_encode($this->response->getRawData()))
            ->setOriginalResponse($this->response)
        ;

        return $subscription;
    }

    /**
     * @param string $productId
     * @return PendingRenewalInfo|null
     */
    private function getSubscriptionData(string $productId): ?PendingRenewalInfo
    {
        foreach ($this->response->getPendingRenewalInfo() as $pendingRenewalInfo) {
            if ($pendingRenewalInfo->getProductId() == $productId) {
                return $pendingRenewalInfo;
            }
        }

        return null;
    }
}