<?php

namespace AnyKey\MobilePaymentsBundle\Providers;

use AnyKey\MobilePaymentsBundle\Composer\PaymentReceipt\GooglePaymentReceiptComposer;
use AnyKey\MobilePaymentsBundle\Interfaces\AbstractProvider;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
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

    /** @var Validator */
    protected $validator;

    /** @var string */
    private $packageName;
    /** @var string|null */
    private $billingKey;
    /** @var array|null */
    private $paymentConfig;

    /**
     * Google constructor.
     * @param bool $enabled
     * @param string|null $packageName
     * @param string|null $billingKey
     * @param string|null $paymentConfig
     * @throws ConfigurationException
     */
    public function __construct(bool $enabled, ?string $packageName, ?string $billingKey, ?string $paymentConfig)
    {
        $this->setEnabled($enabled);
        if ($this->isEnabled()) {
            $this->packageName = $packageName;
            $this->billingKey = $billingKey;
            $this->paymentConfig = \GuzzleHttp\json_decode(base64_decode($paymentConfig), true);
            $this->initValidator();
        }
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param array $config
     * @return SubscriptionReceiptInterface
     * @throws RuntimeException
     * @throws \Exception
     */
    public function validateSubscription(... $config): SubscriptionReceiptInterface
    {
        $receipt = $config[0] ?? null;
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

        return (new GooglePaymentReceiptComposer($response, $receipt))->composeSubscription();
    }

    /**
     * @param array $config
     * @return PurchaseReceiptInterface
     * @throws RuntimeException
     */
    public function validatePurchase(... $config): PurchaseReceiptInterface
    {
        $receipt = $config[0] ?? null;
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

        return (new GooglePaymentReceiptComposer($response, $receipt))->composePurchase();
    }

    /**
     * @throws ConfigurationException
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