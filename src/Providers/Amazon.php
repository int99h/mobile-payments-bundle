<?php

namespace AnyKey\MobilePaymentsBundle\Providers;

use AnyKey\MobilePaymentsBundle\Data\Composer\AmazonReceiptComposer;
use AnyKey\MobilePaymentsBundle\Data\Receipt\AmazonReceiptData;
use AnyKey\MobilePaymentsBundle\Exception\ReceiptException;
use AnyKey\MobilePaymentsBundle\Interfaces\AbstractProvider;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\ReceiptDataInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use GuzzleHttp\Exception\GuzzleException;
use ReceiptValidator\Amazon\Validator;
use ReceiptValidator\Amazon\Response;
use AnyKey\MobilePaymentsBundle\Exception\ConfigurationException;
use AnyKey\MobilePaymentsBundle\Exception\RuntimeException;

/**
 * Class Amazon
 * @package AnyKey\MobilePaymentsBundle\Providers
 */
class Amazon extends AbstractProvider
{
    public const NAME = 'amazon_appstore';

    /** @var Validator */
    protected $validator;

    /** @var bool */
    private $sandbox;
    /** @var string */
    private $endpoint;
    /** @var string|null */
    private $secret;
    /** @var Response */
    private $response;

    /**
     * Amazon constructor.
     * @param bool $enabled
     * @param string|null $mode
     * @param string|null $secret
     * @throws ConfigurationException
     * @throws \ReceiptValidator\RunTimeException
     */
    public function __construct(bool $enabled, ?string $mode, ?string $secret)
    {
        $this->setEnabled($enabled);
        if ($this->isEnabled()) {
            $this->sandbox = ($mode === self::MODE_SANDBOX);
            $this->endpoint = $this->isSandbox() ? Validator::ENDPOINT_SANDBOX : Validator::ENDPOINT_PRODUCTION;
            $this->secret = $secret;
            $this->initValidator();
        }
    }

    /**
     * @return bool
     */
    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * Validate a one-time purchase based payment
     *
     * @param ReceiptDataInterface $receiptData
     * @return PurchaseReceiptInterface
     * @throws ConfigurationException
     * @throws RuntimeException
     */
    public function validatePurchase(ReceiptDataInterface $receiptData): PurchaseReceiptInterface
    {
        $purchase = (new AmazonReceiptComposer(
            $this->validate($receiptData),
            $receiptData,
            $this->isSandbox()
        ))->purchase();

        return $purchase;
    }

    /**
     * Validate a subscription based payment
     * @param ReceiptDataInterface $receiptData
     * @return SubscriptionReceiptInterface
     * @throws ConfigurationException
     * @throws RuntimeException
     * @throws \Exception
     */
    public function validateSubscription(ReceiptDataInterface $receiptData): SubscriptionReceiptInterface
    {
        $subscription = (new AmazonReceiptComposer(
            $this->validate($receiptData),
            $receiptData,
            $this->isSandbox()
        ))->subscription();

        return $subscription;
    }

    /**
     * @param ReceiptDataInterface $receiptData
     * @return Response
     * @throws ConfigurationException
     * @throws RuntimeException
     */
    private function validate(ReceiptDataInterface $receiptData)
    {
        if (!$receiptData instanceof AmazonReceiptData) {
            throw new RuntimeException('Use AmazonReceiptData to validate the receipt.');
        }

        $this->checkAvailability();
        try {
            $response = $this->validator
                ->setUserId($receiptData->getUserId())
                ->setReceiptId($receiptData->getReceipt())
                ->validate()
            ;
        } catch (\Exception | GuzzleException $e) {
            throw new RuntimeException("{$e->getCode()} | {$e->getMessage()}", null, $e);
        }

        $this->response = $response;

        return $response;
    }

    /**
     * @throws ConfigurationException
     * @throws \ReceiptValidator\RunTimeException
     */
    protected function initValidator(): void
    {
        if (!$this->secret) {
            throw new ConfigurationException($this, 'secret not defined');
        }
        $this->validator = (new Validator($this->endpoint))->setDeveloperSecret($this->secret);
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
     * @return Response
     * @throws ReceiptException
     */
    public function getResponse()
    {
        if (!$this->response) {
            throw new ReceiptException('Validate Amazon receipt first.');
        }

        return $this->response;
    }
}