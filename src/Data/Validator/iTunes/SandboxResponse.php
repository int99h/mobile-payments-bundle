<?php

namespace AnyKey\MobilePaymentsBundle\Data\Validator\iTunes;

class SandboxResponse extends AbstractResponse implements ResponseInterface
{
    public function isProduction(): bool
    {
        return false;
    }

    public function isSandbox(): bool
    {
        return true;
    }
}
