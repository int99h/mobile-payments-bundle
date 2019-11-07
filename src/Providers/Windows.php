<?php

namespace AnyKey\MobilePaymentsBundle\Providers;

use AnyKey\MobilePaymentsBundle\Data\Composer\WindowsReceiptComposer;
use AnyKey\MobilePaymentsBundle\Exception\Receipt\InvalidReceiptException;
use AnyKey\MobilePaymentsBundle\Exception\ReceiptException;
use AnyKey\MobilePaymentsBundle\Interfaces\AbstractProvider;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\ReceiptDataInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use ReceiptValidator\WindowsStore\Validator;
use Symfony\Contracts\Cache\CacheInterface;
use AnyKey\MobilePaymentsBundle\Adapters\CacheAdapter;
use AnyKey\MobilePaymentsBundle\Exception\RuntimeException;

/**
 * Class Windows
 * @package AnyKey\MobilePaymentsBundle\Providers
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