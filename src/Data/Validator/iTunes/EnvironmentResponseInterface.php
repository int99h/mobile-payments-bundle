<?php

namespace AnyKey\MobilePaymentsBundle\Data\Validator\iTunes;

interface EnvironmentResponseInterface
{
    public function isSandbox(): bool;

    public function isProduction(): bool;
}
