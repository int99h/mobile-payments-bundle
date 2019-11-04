<?php

namespace AnyKey\MobilePaymentsBundle\Providers;

use AnyKey\MobilePaymentsBundle\Data\Composer\WindowsReceiptComposer;
use AnyKey\MobilePaymentsBundle\Exception\Receipt\InvalidReceiptException;
use AnyKey\MobilePaymentsBundle\Interfaces\AbstractProvider;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
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
     * @param mixed ...$config
     * @return PurchaseReceiptInterface
     * @throws InvalidReceiptException
     * @throws RuntimeException
     */
    public function validatePurchase(...$config): PurchaseReceiptInterface
    {
        $receipt = $config[0] ?? null;
        if (!$this->validate($receipt)) {
            throw new InvalidReceiptException('Invalid Windows purchase receipt.');
        }

        $purchase = (new WindowsReceiptComposer($receipt))->purchase();

        return $purchase;
    }

    /**
     * Validate a subscription based payment
     * @param mixed ...$config
     * @return SubscriptionReceiptInterface
     * @throws InvalidReceiptException
     * @throws RuntimeException
     */
    public function validateSubscription(...$config): SubscriptionReceiptInterface
    {
        $receipt = $config[0] ?? null;
        if (!$this->validate($receipt)) {
            throw new InvalidReceiptException('Invalid Windows subscription receipt.');
        }

        $purchase = (new WindowsReceiptComposer($receipt))->subscription();

        return $purchase;
    }

    /**
     * @param mixed ...$config [string $receipt]
     * @return bool|mixed
     * @throws RuntimeException
     */
    private function validate(...$config)
    {
        $receipt = $config[0] ?? null;
        try {
            $response = $this->validator->validate($receipt);
        } catch (\Exception $e) {
            throw new RuntimeException("{$e->getCode()} | {$e->getMessage()}", null, $e);
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
     * @inheritDoc
     */
    protected function initValidator(): void
    {
        $this->validator = new Validator($this->cache);
    }
}