<?php

namespace AnyKey\MobilePaymentsBundle\Interfaces\Parser;

interface MultipleReceiptsGeneratorInterface
{
    /**
     * @return \Generator|null
     */
    public function generate();
}