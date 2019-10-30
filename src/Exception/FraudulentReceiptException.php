<?php


namespace AnyKey\MobilePaymentsBundle\Exception;


use Throwable;

class FraudulentReceiptException extends \Exception
{
    public function __construct($message = "Fraudulent receipt exception.", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}