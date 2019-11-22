<?php

namespace AnyKey\Exception\Receipt;

use AnyKey\Exception\ReceiptException;
use Throwable;

/**
 * Class InvalidReceiptException
 * @package AnyKey\Exception\Receipt
 */
class InvalidReceiptException extends ReceiptException
{
    /**
     * InvalidReceiptException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "Invalid receipt", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}