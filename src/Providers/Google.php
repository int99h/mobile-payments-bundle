<?php

namespace AnyKey\MobilePaymentsBundle\Providers;

use ReceiptValidator\GooglePlay\PurchaseResponse;
use ReceiptValidator\GooglePlay\SubscriptionResponse;
use ReceiptValidator\GooglePlay\Validator;
use AnyKey\MobilePaymentsBundle\Exception\ConfigurationException;
use AnyKey\MobilePaymentsBundle\Exception\RuntimeException;

/**
 * Class Google
 * @package AnyKey\MobilePaymentsBundle\Providers
 */
class Google extends AbstractProvider
{
    public const NAME = 'google_play';

    /** @var string */
    private $packageName;
    /** @var string */
    private $billingKey;
    /** @var array */
    private $paymentConfig;

    /** @var Validator */
    private $validator;

    /**
     * Google constructor.
     * @param bool $enabled
     * @param string $packageName
     * @param string $billingKey
     * @param string $paymentConfig
     * @throws ConfigurationException
     * @throws \Google_Exception
     */
    public function __construct(bool $enabled, string $packageName, string $billingKey, string $paymentConfig)
    {
        $this->setEnabled($enabled);
        if ($this->isEnabled()) {
            $this->packageName = $packageName;
            $this->billingKey = $billingKey;
            $this->paymentConfig = json_decode(base64_decode($paymentConfig), true);
            $this->initValidator();
        }
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return self::NAME;
    }

    /**
     * @param string $receipt
     * @param bool $modePurchase
     * @return PurchaseResponse|SubscriptionResponse
     * @throws RuntimeException
     */
    public function validate(string $receipt, bool $modePurchase = false)
    {
        $data = json_decode(base64_decode($receipt), true);
        $productId = $data['productId'] ?? null;
        $purchaseToken = $data['purchaseToken'] ?? null;
        try {
            $response = $this->validator
                ->setProductId($productId)
                ->setPurchaseToken($purchaseToken)
                ->setValidationModePurchase($modePurchase)
                ->validate()
            ;
        } catch (\Exception $e) {
            throw new RuntimeException($this, "{$e->getCode()} | {$e->getMessage()}");
        }

        return $response;
    }

    /**
     * @param string $receipt
     * @return SubscriptionResponse
     * @throws RuntimeException
     */
    public function validateSubscription(string $receipt)
    {
        $data = json_decode(base64_decode($receipt), true);
        $productId = $data['productId'] ?? null;
        $purchaseToken = $data['purchaseToken'] ?? null;
        try {
            $response = $this->validator
                ->setProductId($productId)
                ->setPurchaseToken($purchaseToken)
                ->validateSubscription()
            ;
        } catch (\Exception $e) {
            throw new RuntimeException($this, "{$e->getCode()} | {$e->getMessage()}");
        }

        return $response;
    }

    /**
     * @param string $receipt
     * @return PurchaseResponse
     * @throws RuntimeException
     */
    public function validatePurchase(string $receipt)
    {
        $data = json_decode(base64_decode($receipt), true);
        $productId = $data['productId'] ?? null;
        $purchaseToken = $data['purchaseToken'] ?? null;
        try {
            $response = $this->validator
                ->setProductId($productId)
                ->setPurchaseToken($purchaseToken)
                ->validatePurchase()
            ;
        } catch (\Exception $e) {
            throw new RuntimeException("{$e->getCode()} | {$e->getMessage()}", null, $e);
        }

        return $response;
    }

    /**
     * @throws ConfigurationException
     * @throws \Google_Exception
     */
    protected function initValidator(): void
    {
        if (!$this->packageName) {
            throw new ConfigurationException($this, 'package_name not defined');
        }
        if (!$this->billingKey) {
            throw new ConfigurationException($this, 'billing_key not defined');
        }
        if (!$this->paymentConfig || !is_array($this->paymentConfig)) {
            throw new ConfigurationException($this, 'payment_config not defined');
        }
        // init client
        try {
            $client = new \Google_Client();
            $client->setApplicationName($this->packageName);
            $client->setAuthConfig($this->paymentConfig);
            $client->setScopes([\Google_Service_AndroidPublisher::ANDROIDPUBLISHER]);
            $publisher = new \Google_Service_AndroidPublisher($client);
            $this->validator = new Validator($publisher, false);
        } catch (\Google_Exception $e) {
            throw new ConfigurationException($this, 'Google Client Error', null, $e);
        }
    }
}