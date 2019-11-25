<?php

namespace AnyKey\MobilePaymentsBundle\Data\Validator\Amazon;

use AnyKey\MobilePaymentsBundle\Exception\RuntimeException;

class PurchaseItem
{
    /**
     * purchase item info.
     *
     * @var array|null
     */
    protected $_response;

    /**
     * quantity.
     *
     * @var int
     */
    protected $_quantity;

    /**
     * product_id.
     *
     * @var string
     */
    protected $_product_id;

    /**
     * transaction_id.
     *
     * @var string
     */
    protected $_transaction_id;

    /**
     * purchase_date.
     *
     * @var \DateTime
     */
    protected $_purchase_date;

    /**
     * cancellation_date.
     *
     * @var \DateTime
     */
    protected $_cancellation_date;

    /**
     * renewal_date.
     *
     * @var \DateTime
     */
    protected $_renewal_date;

    /**
     * @return array
     */
    public function getRawResponse()
    {
        return $this->_response;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->_quantity;
    }

    /**
     * @return string
     */
    public function getProductId()
    {
        return $this->_product_id;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->_transaction_id;
    }

    /**
     * @return \DateTime
     */
    public function getPurchaseDate()
    {
        return $this->_purchase_date;
    }

    /**
     * @return \DateTime
     */
    public function getCancellationDate()
    {
        return $this->_cancellation_date;
    }

    /**
     * @return \DateTime
     */
    public function getRenewalDate()
    {
        return $this->_renewal_date;
    }

    /**
     * PurchaseItem constructor.
     *
     * @param array|null $jsonResponse
     * @throws RuntimeException
     */
    public function __construct($jsonResponse = null)
    {
        $this->_response = $jsonResponse;
        if ($this->_response !== null) {
            $this->parseJsonResponse();
        }
    }

    /**
     * Parse JSON Response.
     *
     * @return PurchaseItem
     * @throws RuntimeException
     *
     */
    public function parseJsonResponse(): self
    {
        $jsonResponse = $this->_response;
        if (!is_array($jsonResponse)) {
            throw new RuntimeException('Response must be a scalar value');
        }

        if (array_key_exists('quantity', $jsonResponse)) {
            $this->_quantity = $jsonResponse['quantity'];
        }

        if (array_key_exists('receiptId', $jsonResponse)) {
            $this->_transaction_id = $jsonResponse['receiptId'];
        }

        if (array_key_exists('productId', $jsonResponse)) {
            $this->_product_id = $jsonResponse['productId'];
        }

        if (array_key_exists('purchaseDate', $jsonResponse) && !empty($jsonResponse['purchaseDate'])) {
            $this->_purchase_date = (new \DateTime())->setTimestamp(intval(round($jsonResponse['purchaseDate'] / 1000)));
        }

        if (array_key_exists('cancelDate', $jsonResponse) && !empty($jsonResponse['cancelDate'])) {
            $this->_cancellation_date = (new \DateTime())->setTimestamp(
                intval(round($jsonResponse['cancelDate'] / 1000))
            );
        }

        if (array_key_exists('renewalDate', $jsonResponse) && !empty($jsonResponse['renewalDate'])) {
            $this->_renewal_date = (new \DateTime())->setTimestamp(intval(round($jsonResponse['renewalDate'] / 1000)));
        }

        return $this;
    }
}
