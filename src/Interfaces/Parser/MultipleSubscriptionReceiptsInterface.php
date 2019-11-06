<?php

namespace AnyKey\MobilePaymentsBundle\Interfaces\Parser;

use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;

interface MultipleSubscriptionReceiptsInterface
{
    /**
     * @return SubscriptionReceiptInterface[]
     */
    public function create(): array;
}