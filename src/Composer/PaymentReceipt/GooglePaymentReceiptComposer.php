<?php


namespace AnyKey\MobilePaymentsBundle\Composer\PaymentReceipt;

use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use AnyKey\MobilePaymentsBundle\Model\PurchaseReceipt;
use AnyKey\MobilePaymentsBundle\Model\SubscriptionReceipt;
use Google_Service_AndroidPublisher_ProductPurchase;
use http\Exception\RuntimeException;
use ReceiptValidator\GooglePlay\AbstractResponse;
use ReceiptValidator\GooglePlay\PurchaseResponse;
use ReceiptValidator\GooglePlay\SubscriptionResponse;

class GooglePaymentReceiptComposer implements PaymentReceiptComposerInterface
{
    const PAYMENT_STATE_FREE_TRIAL = 2;

    const PURCHASE_TYPE_TEST = 0;
    const PURCHASE_TYPE_PROMO = 1;
    const PURCHASE_TYPE_REWARDED = 2;

    /**
     * @var SubscriptionResponse
     */
    private $response;
    /**
     * @var array
     */
    private $data;
    /**
     * @var string
     */
    private $receipt;

    /**
     * GooglePaymentReceiptComposer constructor.
     * @param AbstractResponse $response
     * @param string $receipt
     */
    public function __construct(AbstractResponse $response, string $receipt)
    {
        $this->response = $response;
        $this->receipt = $receipt;
        $this->data = json_decode(base64_decode($receipt), true);
    }

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     *
     * @return PurchaseReceiptInterface
     */
    public function composePurchase(): PurchaseReceiptInterface
    {
        if (!$this->response instanceof PurchaseResponse) {
            throw new RuntimeException('Google purchase composer cannot process subscription response.');
        }

        return (new PurchaseReceipt())
            ->setProductId($this->data['productId'])
            ->setOrderId($this->data['orderId'])
            ->setRefreshPayload($this->data['purchaseToken'])
            ->setRawResponse($this->getRawResponse())
            ->setOriginalResponse($this->response)
            ->setTransactionId(
                $this->data['orderId'] . '_' . round($this->response->getPurchaseTimeMillis()/1000)
            )
            ->setSandbox($this->isPurchaseResponseSandbox($this->response))
        ;
    }

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     *
     * @return SubscriptionReceiptInterface
     * @throws \Exception
     */
    public function composeSubscription(): SubscriptionReceiptInterface
    {
        if (!$this->response instanceof SubscriptionResponse) {
            throw new RuntimeException('Google subscription composer cannot process purchase response.');
        }

        return (new SubscriptionReceipt())
            ->setProductId($this->data['productId'])
            ->setOrderId($this->data['orderId'])
            ->setRefreshPayload($this->data['purchaseToken'])
            ->setRawResponse($this->getRawResponse())
            ->setOriginalResponse($this->response)
            ->setTransactionId(
                $this->data['orderId'] . '_' . round($this->response->getExpiryTimeMillis()/1000)
            )
            ->setExpiryDate(
                (new \DateTime())->setTimestamp(round($this->response->getExpiryTimeMillis()/1000))
            )
            ->setRenewing($this->response->getAutoRenewing())
            ->setTrial(($this->response->getPaymentState() == self::PAYMENT_STATE_FREE_TRIAL))
            ->setSandbox($this->isSubscriptionResponseSandbox($this->response))
        ;
    }

    /**
     * Return original response after processing the receipt by a payment store
     *
     * @return string
     */
    public function getRawResponse(): ?string
    {
        $decodedPurchaseData = base64_decode($this->receipt);

        if (!$decodedPurchaseData) {
            return null;
        }

        return base64_decode($this->receipt);
    }

    /**
     * @param PurchaseResponse $response
     * @return bool
     */
    private function isPurchaseResponseSandbox(PurchaseResponse $response): bool
    {
        $rawResponse = $response->getRawResponse();

        if ($rawResponse instanceof Google_Service_AndroidPublisher_ProductPurchase) {
            $purchaseType = $rawResponse->getPurchaseType();

            return (
                !is_null($purchaseType) &&
                in_array(
                    $purchaseType,
                    [
                        self::PURCHASE_TYPE_TEST,
                        self::PURCHASE_TYPE_PROMO,
                        self::PURCHASE_TYPE_REWARDED
                    ]
                )
            );
        }

        return false;
    }

    /**
     * @param SubscriptionResponse $response
     * @return bool
     */
    private function isSubscriptionResponseSandbox(SubscriptionResponse $response): bool
    {
        $rawResponse = $response->getRawResponse();

        if ($rawResponse instanceof \Google_Service_AndroidPublisher_SubscriptionPurchase) {
            return ($rawResponse->getPurchaseType() === 0);
        }

        return false;
    }
}