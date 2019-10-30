<?php


namespace AnyKey\MobilePaymentsBundle\Exception;


use Throwable;

class InvalidReceiptException extends \Exception
{
    public function __construct($message = "Invalid receipt exception.", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}