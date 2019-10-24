<?php

namespace AnyKey\MobilePaymentsBundle\Providers;

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
    /** @var bool */
    private $sandbox;
    /** @var string */
    private $endpoint;
    /** @var string */
    private $paymentKey;
    /** @var Validator */
    private $validator;

    /**
     * Apple constructor.
     * @param bool $enabled
     * @param string $mode
     * @param string $paymentKey
     * @throws ConfigurationException
     */
    public function __construct(bool $enabled, string $mode, string $paymentKey)
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
     * @return string
     */
    public function getAlias(): string
    {
        return 'apple_appstore';
    }

    /**
     * @return bool
     */
    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * @param string $receipt
     * @param bool $excludeOld
     * @return ResponseInterface
     * @throws ConfigurationException
     * @throws GuzzleException
     * @throws RuntimeException
     */
    public function validate(string $receipt, bool $excludeOld = false): ResponseInterface
    {
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

        return $response;
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