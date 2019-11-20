<?php

namespace AnyKey\MobilePaymentsBundle\Data\Composer;

use AnyKey\MobilePaymentsBundle\Exception\RuntimeException;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\ReceiptComposerInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\ReceiptDataInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use AnyKey\MobilePaymentsBundle\Data\PurchaseReceipt;
use AnyKey\MobilePaymentsBundle\Data\SubscriptionReceipt;
use Data\Validator\GooglePlay\AbstractResponse;
use Data\Validator\GooglePlay\PurchaseResponse;
use Data\Validator\GooglePlay\SubscriptionResponse;
use Google_Service_AndroidPublisher_ProductPurchase;

/**
 * Class GoogleReceiptComposer
 * @package AnyKey\MobilePaymentsBundle\Data\Creator
 */
class GoogleReceiptComposer implements ReceiptComposerInterface
{
    const PAYMENT_STATE_FREE_TRIAL = 2;

    const PURCHASE_TYPE_TEST = 0;
    const PURCHASE_TYPE_PROMO = 1;
    const PURCHASE_TYPE_REWARDED = 2;

    /** @var AbstractResponse */
    private $response;
    /** @var array */
    private $data;
    /** @var string */
    private $receipt;

    /**
     * GoogleReceiptComposer constructor.
     * @param AbstractResponse $response
     * @param ReceiptDataInterface $receiptData
     */
    public function __construct(AbstractResponse $response, ReceiptDataInterface $receiptData)
    {
        $this->response = $response;
        $this->receipt = $receiptData->getReceipt();
        $this->data = \GuzzleHttp\json_decode(base64_decode($this->receipt), true);
    }

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     * @return PurchaseReceiptInterface
     * @throws RuntimeException
     */
    public function purchase(): PurchaseReceiptInterface
    {
        if (!$this->response instanceof PurchaseResponse) {
            throw new RuntimeException('Google purchase composer cannot process subscription response.');
        }
        $payTime = round($this->response->getPurchaseTimeMillis()/1000);
        $purchase = (new PurchaseReceipt())
            ->setProductId($this->data['productId'])
            ->setOrderId($this->data['orderId'])
            ->setRefreshPayload($this->data['purchaseToken'])
            ->setRawResponse($this->getRawResponse())
            ->setOriginalResponse($this->response)
            ->setTransactionId("{$this->data['orderId']}_{$payTime}")
            ->setSandbox($this->isPurchaseResponseSandbox($this->response))
        ;

        return $purchase;
    }

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     * @return SubscriptionReceiptInterface
     * @throws \Exception
     */
    public function subscription(): SubscriptionReceiptInterface
    {
        if (!$this->response instanceof SubscriptionResponse) {
            throw new RuntimeException('Google subscription composer cannot process purchase response.');
        }
        $expTime = round($this->response->getExpiryTimeMillis()/1000);
        $subscription = (new SubscriptionReceipt())
            ->setProductId($this->data['productId'])
            ->setOrderId($this->data['orderId'])
            ->setRefreshPayload($this->data['purchaseToken'])
            ->setRawResponse($this->getRawResponse())
            ->setOriginalResponse($this->response)
            ->setTransactionId("{$this->data['orderId']}_{$expTime}")
            ->setExpiryDate((new \DateTime())->setTimestamp($expTime))
            ->setRenewing($this->response->getAutoRenewing())
            ->setTrial(($this->response->getPaymentState() == self::PAYMENT_STATE_FREE_TRIAL))
            ->setSandbox($this->isSubscriptionResponseSandbox($this->response))
        ;

        return $subscription;
    }

    /**
     * Return original response after processing the receipt by a payment store
     * @return string
     */
    public function getRawResponse(): ?string
    {
        $data = base64_decode($this->receipt);
        if (!$data) {
            return null;
        }

        return $data;
    }

    /**
     * @param PurchaseResponse $response
     * @return bool
     */
    private function isPurchaseResponseSandbox(PurchaseResponse $response): bool
    {
        $response = $response->getRawResponse();
        if ($response instanceof Google_Service_AndroidPublisher_ProductPurchase) {
            $type = $response->getPurchaseType();
            $isSandBox = (!is_null($type) && in_array($type, [
                        self::PURCHASE_TYPE_TEST,
                        self::PURCHASE_TYPE_PROMO,
                        self::PURCHASE_TYPE_REWARDED,
                    ]));

            return $isSandBox;
        }

        return false;
    }

    /**
     * @param SubscriptionResponse $response
     * @return bool
     */
    private function isSubscriptionResponseSandbox(SubscriptionResponse $response): bool
    {
        $response = $response->getRawResponse();
        if ($response instanceof \Google_Service_AndroidPublisher_SubscriptionPurchase) {
            return ($response->getPurchaseType() === self::PURCHASE_TYPE_TEST);
        }

        return false;
    }
}