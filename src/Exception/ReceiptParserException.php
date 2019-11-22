<?php


namespace AnyKey\Exception;


use Throwable;

class ReceiptParserException extends \Exception
{
    public function __construct($message = "Receipt Parser Exception", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}