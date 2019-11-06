<?php

namespace AnyKey\MobilePaymentsBundle\Interfaces\Parser;

use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;

interface SinglePurchaseReceiptInterface
{
    /**
     * @return PurchaseReceiptInterface|null
     */
    public function create(): ?PurchaseReceiptInterface;
}