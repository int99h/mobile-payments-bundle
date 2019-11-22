<?php

namespace AnyKey\Providers;

use AnyKey\Data\Composer\WindowsReceiptComposer;
use AnyKey\Exception\Receipt\InvalidReceiptException;
use AnyKey\Exception\ReceiptException;
use AnyKey\Interfaces\AbstractProvider;
use AnyKey\Interfaces\PurchaseReceiptInterface;
use AnyKey\Interfaces\ReceiptDataInterface;
use AnyKey\Interfaces\SubscriptionReceiptInterface;
use AnyKey\Data\Validator\WindowsStore\Validator;
use Symfony\Contracts\Cache\CacheInterface;
use AnyKey\Adapters\CacheAdapter;
use AnyKey\Exception\RuntimeException;

/**
 * Class Windows
 * @package AnyKey\Providers
 */
class Windows extends AbstractProvider
{
    public const NAME = 'windows_store';

    /** @var Validator */
    protected $validator;
    /** @var CacheAdapter */
    private $cache;
    /** @var bool */
    private $response;

    /**
     * Windows constructor.
     * @param bool $enabled
     * @param CacheInterface|null $cache
     */
    public function __construct(bool $enabled, CacheInterface $cache = null)
    {
        $this->setEnabled($enabled);
        if ($this->isEnabled()) {
            if ($cache) {
                $this->cache = new CacheAdapter($cache);
            }
            $this->initValidator();
        }
    }

    /**
     * Validate a one-time purchase based payment
     *
     * @param ReceiptDataInterface $receiptData
     * @return PurchaseReceiptInterface
     * @throws InvalidReceiptException
     * @throws RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validatePurchase(ReceiptDataInterface $receiptData): PurchaseReceiptInterface
    {
        if (!$this->validate($receiptData)) {
            throw new InvalidReceiptException('Invalid Windows purchase receipt.');
        }

        $purchase = (new WindowsReceiptComposer($receiptData))->purchase();

        return $purchase;
    }

    /**
     * Validate a subscription based payment
     * @param ReceiptDataInterface $receiptData
     * @return SubscriptionReceiptInterface
     * @throws InvalidReceiptException
     * @throws RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateSubscription(ReceiptDataInterface $receiptData): SubscriptionReceiptInterface
    {
        if (!$this->validate($receiptData)) {
            throw new InvalidReceiptException('Invalid Windows subscription receipt.');
        }

        $subscription = (new WindowsReceiptComposer($receiptData))->subscription();

        return $subscription;
    }

    /**
     * @param ReceiptDataInterface $receiptData
     * @return bool|mixed
     * @throws RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function validate(ReceiptDataInterface $receiptData)
    {
        try {
            $response = $this->validator->validate($receiptData->getReceipt());
        } catch (\Exception $e) {
            throw new RuntimeException("{$e->getCode()} | {$e->getMessage()}", null, $e);
        }

        $this->response = $response;

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
     * @inheritDoc
     */
    protected function initValidator(): void
    {
        $this->validator = new Validator($this->cache);
    }

    /**
     * Retrieve the original response from the payment provider
     * @throws ReceiptException
     * @return bool
     */
    public function getResponse()
    {
        if (!$this->response) {
            throw new ReceiptException('Validate Windows receipt first.');
        }

        return $this->response;
    }
}