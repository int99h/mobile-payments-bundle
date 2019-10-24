<?php

namespace AnyKey\MobilePaymentsBundle\Exception;

use Throwable;

/**
 * Class RuntimeException
 * @package AnyKey\MobilePaymentsBundle\Exception
 */
class RuntimeException extends GeneralException
{
    /**
     * RuntimeException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "Runtime error", $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}