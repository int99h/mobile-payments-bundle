<?php

namespace AnyKey\MobilePaymentsBundle\Providers;

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
    /** @var bool */
    private $sandbox;
    /** @var string */
    private $endpoint;
    /** @var string */
    private $secret;

    /** @var Validator */
    private $validator;

    /**
     * Amazon constructor.
     * @param bool $enabled
     * @param string $mode
     * @param string|null $secret
     * @throws ConfigurationException
     * @throws \ReceiptValidator\RunTimeException
     */
    public function __construct(bool $enabled, string $mode, ?string $secret)
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
     * @return string
     */
    public function getAlias(): string
    {
        return 'amazon_appstore';
    }

    /**
     * @return bool
     */
    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * @param string $userId
     * @param string $receiptId
     * @return Response
     * @throws ConfigurationException
     * @throws RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validate(string $userId, string $receiptId): Response
    {
        $this->checkAvailability();
        try {
            $response = $this->validator
                ->setUserId($userId)
                ->setReceiptId($receiptId)
                ->validate()
            ;
        } catch (\Exception $e) {
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
}