<?php

namespace AnyKey\Data\Validator\iTunes;

interface EnvironmentResponseInterface
{
    public function isSandbox(): bool;

    public function isProduction(): bool;
}
