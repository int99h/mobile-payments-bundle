<?php

namespace AnyKey\MobilePaymentsBundle\Exception;

use Throwable;
use AnyKey\MobilePaymentsBundle\Interfaces\ProviderInterface;

/**
 * Class ConfigurationException
 * @package AnyKey\MobilePaymentsBundle\Exception
 */
class ConfigurationException extends GeneralException
{
    /**
     * ConfigurationException constructor.
     * @param ProviderInterface $provider
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        ProviderInterface $provider,
        $message = "",
        $code = 500,
        Throwable $previous = null
    )
    {
        parent::__construct(
            "{$provider::getName()} configuration error: {$message}",
            $code,
            $previous
        );
    }
}