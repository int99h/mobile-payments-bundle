<?php


namespace AnyKey\MobilePaymentsBundle\Data\Composer;


use AnyKey\MobilePaymentsBundle\Data\Receipt\AmazonReceiptData;
use AnyKey\MobilePaymentsBundle\Exception\RuntimeException;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\ReceiptComposerInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\ReceiptDataInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use AnyKey\MobilePaymentsBundle\Data\PurchaseReceipt;
use AnyKey\MobilePaymentsBundle\Data\SubscriptionReceipt;
use ReceiptValidator\Amazon\Response;

class AmazonReceiptComposer implements ReceiptComposerInterface
{
    /**
     * @var Response
     */
    private $response;
    /**
     * @var bool
     */
    private $isSandbox;
    /**
     * @var AmazonReceiptData
     */
    private $receiptData;

    /**
     * AmazonReceiptComposer constructor.
     * @param Response $response
     * @param ReceiptDataInterface $receiptData
     * @param bool $isSandbox
     * @throws RuntimeException
     */
    public function __construct(Response $response, ReceiptDataInterface $receiptData, bool $isSandbox)
    {
        $this->response = $response;
        $this->isSandbox = $isSandbox;

        if (!$receiptData instanceof AmazonReceiptData) {
            throw new RuntimeException('Use AmazonReceiptData to validate the receipt.');
        }
        $this->receiptData = $receiptData;
    }

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     * @return SubscriptionReceiptInterface
     * @throws \Exception
     */
    public function subscription(): SubscriptionReceiptInterface
    {
        $latestReceipt = $this->response->getPurchases()[0];

        $payTime = round($latestReceipt->getPurchaseDate()->toDateTime()->getTimestamp()/1000);

        $isRenewing = !is_null($latestReceipt->getRenewalDate());
        if ($isRenewing) {
            $expiryDate = $latestReceipt->getRenewalDate()->toDateTime();
        } else {
            $expiryDate = $latestReceipt->getCancellationDate()->toDateTime();
        }

        if (!$this->receiptData->getSubscriptionTrialTimestamp()) {
            $isTrial = false;
        } else {
            $trialEndDate = $latestReceipt->getPurchaseDate()->getTimestamp() +
                $this->receiptData->getSubscriptionTrialTimestamp();
            $subscriptionTrialEndDate = (new \DateTime())->setTimestamp($trialEndDate);

            // if subscription + trial dates end later than current date, then it's trial
            $isTrial = $subscriptionTrialEndDate > (new \DateTime())->getTimestamp();
        }

        $receipt = (new SubscriptionReceipt())
            ->setProductId($latestReceipt->getProductId())
            ->setTransactionId("{$latestReceipt->getTransactionId()}_{$payTime}")
            ->setOrderId($latestReceipt->getTransactionId())
            ->setRefreshPayload($this->getRefreshPayload())
            ->setSandbox($this->isSandbox)
            ->setRawResponse($this->getRawResponse())
            ->setOriginalResponse($this->response)
            ->setExpiryDate($expiryDate)
            ->setRenewing($isRenewing)
            ->setTrial($isTrial)
        ;

        return $receipt;
    }

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     * @return PurchaseReceiptInterface
     */
    public function purchase(): PurchaseReceiptInterface
    {
        $latestReceipt = $this->response->getPurchases()[0];
        $payTime = round($latestReceipt->getPurchaseDate()->toDateTime()->getTimestamp()/1000);
        $receipt = (new PurchaseReceipt())
            ->setProductId($latestReceipt->getProductId())
            ->setTransactionId("{$latestReceipt->getTransactionId()}_{$payTime}")
            ->setOrderId($latestReceipt->getTransactionId())
            ->setRefreshPayload($this->getRefreshPayload())
            ->setSandbox($this->isSandbox)
            ->setRawResponse($this->getRawResponse())
            ->setOriginalResponse($this->response)
        ;

        return $receipt;
    }

    /**
     * @return string
     */
    private function getRawResponse(): string
    {
        return json_encode($this->response->getReceipt());
    }

    /**
     * @return string
     */
    private function getRefreshPayload(): string
    {
        return base64_encode(json_encode([
            'user_id' => $this->receiptData->getUserId(),
            'receipt_id' => $this->receiptData->getReceipt()
        ]));
    }
}