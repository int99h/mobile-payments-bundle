<?php

namespace AnyKey\MobilePaymentsBundle\Data\Validator\GooglePlay;

/**
 * Class SubscriptionResponse
 * @package AnyKey\MobilePaymentsBundle\Data\Validator\GooglePlay
 */
class SubscriptionResponse extends AbstractResponse
{
    /**
     * @var \Google_Service_AndroidPublisher_SubscriptionPurchase
     */
    protected $response;

    /**
     * @return bool
     */
    public function getAutoRenewing()
    {
        return (bool)$this->response->getAutoRenewing();
    }

    /**
     * @return integer|null
     */
    public function getCancelReason()
    {
        return $this->response->getCancelReason();
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->response->getCountryCode();
    }

    /**
     * @return integer
     */
    public function getPriceAmountMicros()
    {
        return $this->response->getPriceAmountMicros();
    }

    /**
     * @return string
     */
    public function getPriceCurrencyCode()
    {
        return $this->response->getPriceCurrencyCode();
    }

    /**
     * @return string
     */
    public function getStartTimeMillis()
    {
        return $this->response->getStartTimeMillis();
    }

    /**
     * @return integer
     */
    public function getExpiryTimeMillis()
    {
        return $this->response->getExpiryTimeMillis();
    }

    /**
     * @return integer|null
     */
    public function getUserCancellationTimeMillis()
    {
        return $this->response->getUserCancellationTimeMillis();
    }

    /**
     * @return integer
     */
    public function getPaymentState()
    {
        return $this->response->getPaymentState();
    }

    /**
     * @return string
     * @deprecated Use getExpiryTimeMillis() method instead
     */
    public function getExpiresDate()
    {
        return $this->response->expiryTimeMillis;
    }
}
