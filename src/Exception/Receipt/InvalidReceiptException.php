<?php

namespace AnyKey\MobilePaymentsBundle\Exception\Receipt;

use AnyKey\MobilePaymentsBundle\Exception\ReceiptException;
use Throwable;

/**
 * Class InvalidReceiptException
 * @package AnyKey\MobilePaymentsBundle\Exception\Receipt
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