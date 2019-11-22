<?php

namespace AnyKey\Data;

use AnyKey\Interfaces\SubscriptionReceiptInterface;

/**
 * Class SubscriptionReceipt
 * @package AnyKey\Data
 */
class SubscriptionReceipt implements SubscriptionReceiptInterface
{
    /** @var string|null */
    private $productId = null;
    /** @var \DateTime|null */
    private $expiryDate = null;
    /** @var string|null */
    private $refreshPayload = null;
    /** @var bool|null */
    private $renewing = null;
    /** @var string|null */
    private $transactionId = null;
    /** @var string|null */
    private $orderId = null;
    /** @var null|bool */
    private $trial = null;
    /** @var bool */
    private $sandbox;
    /** @var string|null */
    private $rawResponse = null;
    /** @var mixed|null */
    private $originalResponse = null;

    /**
     * Check if receipt is expired
     * @return bool
     * @throws \Exception
     */
    public function isExpired(): bool
    {
        return $this->expiryDate ? $this->expiryDate->getTimestamp() > (new \DateTime())->getTimestamp() : false;
    }

    /**
     * Get expiry date of the payment
     * @return \DateTime
     */
    public function getExpiryDate(): \DateTime
    {
        return $this->expiryDate;
    }

    /**
     * Get product ID of the payment
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * Get transaction ID of the payment
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * Get order ID of the payment
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * Is payment renewing
     * @return bool
     */
    public function isRenewing(): bool
    {
        return $this->renewing;
    }

    /**
     * Get refresh payload of the payment
     * @return string
     */
    public function getRefreshPayload(): string
    {
        return $this->refreshPayload;
    }

    /**
     * Is payment receipt trial
     * @return bool
     */
    public function isTrial(): bool
    {
        return $this->trial;
    }

    /**
     * Is payment receipt in sandbox mode (test payment)
     * @return bool
     */
    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * Return original response after processing the receipt by a payment store
     * @return string
     */
    public function getRawResponse(): ?string
    {
        return $this->rawResponse;
    }

    /**
     * Returns an original response object of a payment provider
     * @return mixed|null
     */
    public function getOriginalResponse()
    {
        return $this->originalResponse;
    }

    /**
     * @param string|null $productId
     * @return SubscriptionReceipt
     */
    public function setProductId(?string $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * @param string|null $refreshPayload
     * @return SubscriptionReceipt
     */
    public function setRefreshPayload(?string $refreshPayload): self
    {
        $this->refreshPayload = $refreshPayload;

        return $this;
    }

    /**
     * @param string|null $transactionId
     * @return SubscriptionReceipt
     */
    public function setTransactionId(?string $transactionId): self
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * @param string|null $orderId
     * @return SubscriptionReceipt
     */
    public function setOrderId(?string $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @param bool|null $trial
     * @return SubscriptionReceipt
     */
    public function setTrial(?bool $trial): self
    {
        $this->trial = $trial;

        return $this;
    }

    /**
     * @param bool $sandbox
     * @return SubscriptionReceipt
     */
    public function setSandbox(bool $sandbox): self
    {
        $this->sandbox = $sandbox;

        return $this;
    }

    /**
     * @param string|null $rawResponse
     * @return SubscriptionReceipt
     */
    public function setRawResponse(?string $rawResponse): self
    {
        $this->rawResponse = $rawResponse;

        return $this;
    }

    /**
     * @param mixed|null $originalResponse
     * @return SubscriptionReceipt
     */
    public function setOriginalResponse($originalResponse = null): self
    {
        $this->originalResponse = $originalResponse;

        return $this;
    }

    /**
     * @param bool|null $renewing
     * @return SubscriptionReceipt
     */
    public function setRenewing(?bool $renewing): self
    {
        $this->renewing = $renewing;

        return $this;
    }

    /**
     * @param \DateTime|null $expiryDate
     * @return SubscriptionReceipt
     */
    public function setExpiryDate(?\DateTime $expiryDate): self
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }
}