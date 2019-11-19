<?php


namespace AnyKey\MobilePaymentsBundle\Data;

use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;

/**
 * Class PurchaseReceipt
 * @package AnyKey\MobilePaymentsBundle\Data
 */
class PurchaseReceipt implements PurchaseReceiptInterface
{
    /** @var string|null */
    private $productId = null;
    /** @var string|null */
    private $refreshPayload = null;
    /** @var string|null */
    private $transactionId = null;
    /** @var string|null */
    private $orderId = null;
    /** @var bool */
    private $sandbox;
    /** @var string|null */
    private $rawResponse = null;
    /** @var mixed|null */
    private $originalResponse = null;

    /**
     * Get product ID of the payment
     *
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * Get transaction ID of the payment
     *
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * Get order ID of the payment
     *
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * Get refresh payload of the payment
     *
     * @return string
     */
    public function getRefreshPayload(): string
    {
        return $this->refreshPayload;
    }

    /**
     * Is payment receipt in sandbox mode (test payment)
     *
     * @return bool
     */
    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * Return original response after processing the receipt by a payment store
     *
     * @return string
     */
    public function getRawResponse(): ?string
    {
        return $this->rawResponse;
    }

    /**
     * Returns an original response object of a payment provider
     *
     * @return mixed|null
     */
    public function getOriginalResponse()
    {
        return $this->originalResponse;
    }

    /**
     * @param string|null $productId
     * @return PurchaseReceipt
     */
    public function setProductId(?string $productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @param string|null $refreshPayload
     * @return PurchaseReceipt
     */
    public function setRefreshPayload(?string $refreshPayload): self
    {
        $this->refreshPayload = $refreshPayload;
        return $this;
    }

    /**
     * @param string|null $transactionId
     * @return PurchaseReceipt
     */
    public function setTransactionId(?string $transactionId): self
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    /**
     * @param string|null $orderId
     * @return PurchaseReceipt
     */
    public function setOrderId(?string $orderId): self
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @param bool $sandbox
     * @return PurchaseReceipt
     */
    public function setSandbox(bool $sandbox): self
    {
        $this->sandbox = $sandbox;
        return $this;
    }

    /**
     * @param string|null $rawResponse
     * @return PurchaseReceipt
     */
    public function setRawResponse(?string $rawResponse): self
    {
        $this->rawResponse = $rawResponse;
        return $this;
    }

    /**
     * @param mixed|null $originalResponse
     * @return PurchaseReceipt
     */
    public function setOriginalResponse($originalResponse = null): self
    {
        $this->originalResponse = $originalResponse;
        return $this;
    }
}