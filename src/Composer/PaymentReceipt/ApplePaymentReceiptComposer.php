<?php


namespace AnyKey\MobilePaymentsBundle\Composer\PaymentReceipt;


use AnyKey\MobilePaymentsBundle\Exception\RuntimeException;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use AnyKey\MobilePaymentsBundle\Model\PurchaseReceipt;
use AnyKey\MobilePaymentsBundle\Model\SubscriptionReceipt;
use ReceiptValidator\iTunes\PendingRenewalInfo;
use ReceiptValidator\iTunes\ResponseInterface;

class ApplePaymentReceiptComposer implements PaymentReceiptComposerInterface
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * ApplePaymentReceiptComposer constructor.
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     *
     * @return PurchaseReceiptInterface
     */
    public function composePurchase(): PurchaseReceiptInterface
    {
        $latestReceipt = $this->response->getLatestReceiptInfo()[0];

        return (new PurchaseReceipt())
            ->setProductId($latestReceipt->getProductId())
            ->setTransactionId($latestReceipt->getTransactionId())
            ->setOrderId($latestReceipt->getOriginalTransactionId())
            ->setRefreshPayload($this->response->getLatestReceipt())
            ->setSandbox($this->response->isSandbox())
            ->setRawResponse(json_encode($this->response->getRawData()))
            ->setOriginalResponse($this->response)
        ;
    }

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     *
     * @return SubscriptionReceiptInterface
     * @throws RuntimeException
     */
    public function composeSubscription(): SubscriptionReceiptInterface
    {
        $latestReceipt = $this->response->getLatestReceiptInfo()[0];

        if (!$latestReceipt) {
            throw new RuntimeException('Cannot retrieve the latest receipt.');
        }

        $pendingRenewalInfo = $this->getPendingRenewalInfoForLatestReceipt($latestReceipt->getProductId());

        return (new SubscriptionReceipt())
            ->setExpiryDate($latestReceipt->getExpiresDate()->toDateTime())
            ->setProductId($latestReceipt->getProductId())
            ->setTransactionId($latestReceipt->getTransactionId())
            ->setOrderId($latestReceipt->getOriginalTransactionId())
            ->setRenewing($pendingRenewalInfo ? $pendingRenewalInfo->getAutoRenewStatus() : null)
            ->setRefreshPayload($this->response->getLatestReceipt())
            ->setTrial($latestReceipt->isTrialPeriod())
            ->setSandbox($this->response->isSandbox())
            ->setRawResponse(json_encode($this->response->getRawData()))
            ->setOriginalResponse($this->response)
        ;
    }

    /**
     * @param string $productId
     * @return PendingRenewalInfo|null
     */
    private function getPendingRenewalInfoForLatestReceipt(string $productId): ?PendingRenewalInfo
    {
        foreach ($this->response->getPendingRenewalInfo() as $pendingRenewalInfo) {
            if ($pendingRenewalInfo->getProductId() == $productId) {
                return $pendingRenewalInfo;
            }
        }

        return null;
    }
}