<?php

namespace AnyKey\MobilePaymentsBundle\Providers;

use AnyKey\MobilePaymentsBundle\Data\Composer\AmazonReceiptComposer;
use AnyKey\MobilePaymentsBundle\Interfaces\AbstractProvider;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
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
     * @param mixed ...$config
     * @return PurchaseReceiptInterface
     * @throws ConfigurationException
     * @throws RuntimeException
     */
    public function validatePurchase(...$config): PurchaseReceiptInterface
    {
        $userId = $config[0] ?? null;
        $receiptId = $config[1] ?? null;

        $purchase = (new AmazonReceiptComposer(
            $this->validate($userId, $receiptId),
            $userId,
            $receiptId,
            $this->isSandbox()
        ))->purchase();

        return $purchase;
    }

    /**
     * Validate a subscription based payment
     * @param mixed ...$config
     * @return SubscriptionReceiptInterface
     * @throws ConfigurationException
     * @throws RuntimeException
     */
    public function validateSubscription(...$config): SubscriptionReceiptInterface
    {
        $userId = $config[0] ?? null;
        $receiptId = $config[1] ?? null;

        $subscription = (new AmazonReceiptComposer(
            $this->validate($userId, $receiptId),
            $userId,
            $receiptId,
            $this->isSandbox()
        ))->subscription();

        return $subscription;
    }

    /**
     * @param mixed ...$config [string $userId, string $receiptId]
     * @return Response
     * @throws ConfigurationException
     * @throws RuntimeException
     */
    private function validate(...$config)
    {
        $userId = $config[0] ?? null;
        $receiptId = $config[1] ?? null;
        $this->checkAvailability();
        try {
            $response = $this->validator
                ->setUserId($userId)
                ->setReceiptId($receiptId)
                ->validate()
            ;
        } catch (\Exception | GuzzleException $e) {
            throw new RuntimeException("{$e->getCode()} | {$e->getMessage()}", null, $e);
        }

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
}