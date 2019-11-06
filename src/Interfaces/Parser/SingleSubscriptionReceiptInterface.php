<?php

namespace AnyKey\MobilePaymentsBundle\Interfaces\Parser;

use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;

interface SingleSubscriptionReceiptInterface
{
    /**
     * @return SubscriptionReceiptInterface|null
     */
    public function create(): ?SubscriptionReceiptInterface;
}