<?php


namespace AnyKey\MobilePaymentsBundle\Interfaces;


interface PurchaseReceiptInterface
{
    /**
     * Get product ID of the payment
     *
     * @return string
     */
    public function getProductId(): string;

    /**
     * Get transaction ID of the payment
     *
     * @return string
     */
    public function getTransactionId(): string;

    /**
     * Get order ID of the payment
     *
     * @return string
     */
    public function getOrderId(): string;

    /**
     * Get refresh payload of the payment
     *
     * @return string
     */
    public function getRefreshPayload(): string;

    /**
     * Is payment receipt in sandbox mode (test payment)
     *
     * @return bool
     */
    public function isSandbox(): bool;

    /**
     * Return original response after processing the receipt by a payment store
     *
     * @return string
     */
    public function getRawResponse(): ?string;

    /**
     * Returns an original response object of a payment provider
     *
     * @return mixed
     */
    public function getOriginalResponse();
}