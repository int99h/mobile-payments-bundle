<?php

namespace AnyKey\MobilePaymentsBundle\Providers;

use AnyKey\MobilePaymentsBundle\Data\Composer\AppleReceiptComposer;
use AnyKey\MobilePaymentsBundle\Exception\GeneralException;
use AnyKey\MobilePaymentsBundle\Exception\Receipt\FraudException;
use AnyKey\MobilePaymentsBundle\Exception\Receipt\InvalidReceiptException;
use AnyKey\MobilePaymentsBundle\Exception\ReceiptException;
use AnyKey\MobilePaymentsBundle\Interfaces\AbstractProvider;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\ReceiptDataInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use ReceiptValidator\iTunes\ResponseInterface;
use ReceiptValidator\iTunes\Validator;
use AnyKey\MobilePaymentsBundle\Exception\ConfigurationException;
use AnyKey\MobilePaymentsBundle\Exception\RuntimeException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Apple
 * @package AnyKey\MobilePaymentsBundle\Providers
 */
class Apple extends AbstractProvider
{
    public const NAME = 'apple_appstore';

    /** @var Validator */
    protected $validator;

    /** @var bool */
    private $sandbox;
    /** @var string */
    private $endpoint;
    /** @var string|null */
    private $paymentKey;

    /**
     * Apple constructor.
     * @param bool $enabled
     * @param string|null $mode
     * @param string|null $paymentKey
     * @throws ConfigurationException
     */
    public function __construct(bool $enabled, ?string $mode, ?string $paymentKey)
    {
        $this->setEnabled($enabled);
        if ($this->isEnabled()) {
            $this->sandbox = ($mode === self::MODE_SANDBOX);
            $this->endpoint = $this->isSandbox() ? Validator::ENDPOINT_SANDBOX : Validator::ENDPOINT_PRODUCTION;
            $this->paymentKey = $paymentKey;
            $this->initValidator();
        }
    }

    /**
     * Validate a one-time purchase based payment
     * @param ReceiptDataInterface $receiptData
     * @return PurchaseReceiptInterface
     * @throws ReceiptException
     */
    public function validatePurchase(ReceiptDataInterface $receiptData): PurchaseReceiptInterface
    {
        try {
            $purchase = (new AppleReceiptComposer($this->validate($receiptData)))->purchase();
        } catch (GuzzleException | GeneralException $e) {
            throw new ReceiptException($e->getMessage());
        }

        return $purchase;
    }

    /**
     * Validate a subscription based payment
     * @param ReceiptDataInterface $receiptData
     * @return SubscriptionReceiptInterface
     * @throws ReceiptException
     */
    public function validateSubscription(ReceiptDataInterface $receiptData): SubscriptionReceiptInterface
    {
        try {
            $subscription = (new AppleReceiptComposer($this->validate($receiptData)))->subscription();
        } catch (GuzzleException | GeneralException $e) {
            throw new ReceiptException($e->getMessage());
        }

        return $subscription;
    }

    /**
     * @param ReceiptDataInterface $receiptData
     * @return mixed|ResponseInterface
     * @throws ConfigurationException
     * @throws FraudException
     * @throws GuzzleException
     * @throws InvalidReceiptException
     * @throws RuntimeException
     */
    private function validate(ReceiptDataInterface $receiptData): ResponseInterface
    {
        $this->checkAvailability();
        try {
            $response = $this->validator
                ->setExcludeOldTransactions($receiptData->getOptions()['exclude_old'])
                ->setReceiptData($receiptData->getReceipt())
                ->validate()
            ;
        } catch (\Exception $e) {
            throw new RuntimeException("{$e->getCode()} | {$e->getMessage()}", null, $e);
        }
        if (!$response->isValid()) {
            throw new InvalidReceiptException();
        }
        if (!$response->getLatestReceiptInfo()) {
            throw new FraudException('Fraudulent Apple Receipt.');
        }

        return $response;
    }

    /**
     * @return bool
     */
    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * @throws ConfigurationException
     */
    protected function initValidator(): void
    {
        if (!$this->paymentKey) {
            throw new ConfigurationException($this, 'payment_key not defined');
        }
        $this->validator = (new Validator($this->endpoint))->setSharedSecret($this->paymentKey);
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return self::NAME;
    }
}