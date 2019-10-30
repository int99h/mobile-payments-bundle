<?php

namespace AnyKey\MobilePaymentsBundle\Exception;

use Throwable;

/**
 * Class ReceiptException
 * @package AnyKey\MobilePaymentsBundle\Exception
 */
class ReceiptException extends GeneralException
{
    /**
     * InvalidReceiptException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "Receipt error", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}