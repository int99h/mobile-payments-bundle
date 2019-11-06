<?php

namespace AnyKey\MobilePaymentsBundle\Interfaces\Parser;

interface ReceiptsGeneratorInterface
{
    /**
     * @return \Generator|null
     */
    public function generate();
}