<?php

namespace AnyKey\MobilePaymentsBundle\Providers;

use AnyKey\MobilePaymentsBundle\Composer\PaymentReceipt\ApplePaymentReceiptComposer;
use AnyKey\MobilePaymentsBundle\Exception\FraudulentReceiptException;
use AnyKey\MobilePaymentsBundle\Exception\InvalidReceiptException;
use AnyKey\MobilePaymentsBundle\Interfaces\AbstractProvider;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
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
     *
     * @param mixed ...$config
     * @return PurchaseReceiptInterface
     * @throws ConfigurationException
     * @throws FraudulentReceiptException
     * @throws GuzzleException
     * @throws InvalidReceiptException
     * @throws RuntimeException
     */
    public function validatePurchase(...$config): PurchaseReceiptInterface
    {
        return (new ApplePaymentReceiptComposer($this->validate($config)))->composePurchase();
    }

    /**
     * Validate a subscription based payment
     * @param mixed ...$config
     * @return SubscriptionReceiptInterface
     * @throws ConfigurationException
     * @throws FraudulentReceiptException
     * @throws GuzzleException
     * @throws InvalidReceiptException
     * @throws RuntimeException
     */
    public function validateSubscription(...$config): SubscriptionReceiptInterface
    {
        return (new ApplePaymentReceiptComposer($this->validate($config)))->composeSubscription();
    }

    /**
     * @param mixed ...$config [string $receipt, bool $excludeOld]
     * @return mixed|ResponseInterface
     * @throws ConfigurationException
     * @throws GuzzleException
     * @throws RuntimeException
     * @throws InvalidReceiptException
     * @throws FraudulentReceiptException
     */
    private function validate(...$config): ResponseInterface
    {
        $receipt = $config[0] ?? null;
        $excludeOld = $config[1] ?? false;
        $this->checkAvailability();
        try {
            $response = $this->validator
                ->setExcludeOldTransactions($excludeOld)
                ->setReceiptData($receipt)
                ->validate()
            ;
        } catch (\Exception $e) {
            throw new RuntimeException("{$e->getCode()} | {$e->getMessage()}", null, $e);
        }

        if (!$response->isValid()) {
            throw new InvalidReceiptException();
        }

        if (!$response->getLatestReceiptInfo()) {
            throw new FraudulentReceiptException('Fraudulent Apple Receipt.');
        }

        return $response;
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return self::NAME;
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
}