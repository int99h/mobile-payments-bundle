<?php

namespace AnyKey\MobilePaymentsBundle\Providers;

use AnyKey\MobilePaymentsBundle\Interfaces\AbstractProvider;
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

    /** @var CacheAdapter */
    private $cache;
    /** @var Validator */
    private $validator;

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
     * @return string
     */
    public static function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param string $receipt
     * @return bool
     * @throws RuntimeException
     */
    public function validate(string $receipt)
    {
        try {
            $response = $this->validator->validate($receipt);
        } catch (\Exception $e) {
            throw new RuntimeException("{$e->getCode()} | {$e->getMessage()}", null, $e);
        }

        return $response;
    }

    /**
     * @inheritDoc
     */
    protected function initValidator(): void
    {
        $this->validator = new Validator($this->cache);
    }
}