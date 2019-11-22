<?php

namespace AnyKey\Providers;

use AnyKey\Data\Composer\AppleReceiptComposer;
use AnyKey\Data\Receipt\AppleReceiptData;
use AnyKey\Exception\GeneralException;
use AnyKey\Exception\Receipt\FraudException;
use AnyKey\Exception\Receipt\InvalidReceiptException;
use AnyKey\Exception\ReceiptException;
use AnyKey\Interfaces\AbstractProvider;
use AnyKey\Interfaces\PurchaseReceiptInterface;
use AnyKey\Interfaces\ReceiptDataInterface;
use AnyKey\Interfaces\SubscriptionReceiptInterface;
use AnyKey\Data\Validator\iTunes\ResponseInterface;
use AnyKey\Data\Validator\iTunes\Validator;
use AnyKey\Exception\ConfigurationException;
use AnyKey\Exception\RuntimeException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Apple
 * @package AnyKey\Providers
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
    /** @var ResponseInterface */
    private $response;

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
     * @throws \AnyKey\Exception\ReceiptParserException
     * @throws RuntimeException
     */
    public function validatePurchase(ReceiptDataInterface $receiptData): PurchaseReceiptInterface
    {
        if (!$receiptData instanceof AppleReceiptData) {
            throw new RuntimeException('Use AppleReceiptData to validate the receipt.');
        }

        try {
            $purchase = (new AppleReceiptComposer(
                $this->check($receiptData),
                $receiptData->getReceiptGenerator()
            ))->purchase();
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
     * @throws \AnyKey\Exception\ReceiptParserException
     * @throws RuntimeException
     */
    public function validateSubscription(ReceiptDataInterface $receiptData): SubscriptionReceiptInterface
    {
        if (!$receiptData instanceof AppleReceiptData) {
            throw new RuntimeException('Use AppleReceiptData to validate the receipt.');
        }

        try {
            $subscription = (new AppleReceiptComposer(
                $this->check($receiptData),
                $receiptData->getReceiptGenerator()
            ))->subscription();
        } catch (GuzzleException | GeneralException $e) {
            throw new ReceiptException($e->getMessage());
        }

        return $subscription;
    }

    /**
     * @param ReceiptDataInterface $receiptData
     * @return ResponseInterface
     * @throws ConfigurationException
     * @throws FraudException
     * @throws GuzzleException
     * @throws InvalidReceiptException
     * @throws RuntimeException
     */
    public function check(ReceiptDataInterface $receiptData): ResponseInterface
    {
        $this->checkAvailability();
        try {
            $response = $this->validator
                ->setExcludeOldTransactions($receiptData->isExcludeOld())
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

        $this->response = $response;

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

    /**
     * Retrieve the original response from the payment provider
     * @return ResponseInterface
     * @throws ReceiptException
     */
    public function getResponse()
    {
        if (!$this->response) {
            throw new ReceiptException('Validate Apple receipt first.');
        }

        return $this->response;
    }
}