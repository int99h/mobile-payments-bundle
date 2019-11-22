<?php

namespace AnyKey\Exception\Receipt;

use AnyKey\Exception\ReceiptException;
use Throwable;

/**
 * Class FraudException
 * @package AnyKey\Exception\Receipt
 */
class FraudException extends ReceiptException
{
    /**
     * FraudException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "Fraudulent receipt", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}