<?php

namespace AnyKey\MobilePaymentsBundle\Data\Validator\iTunes;

class ProductionResponse extends AbstractResponse implements ResponseInterface
{
    public function isProduction(): bool
    {
        return true;
    }

    public function isSandbox(): bool
    {
        return false;
    }
}
