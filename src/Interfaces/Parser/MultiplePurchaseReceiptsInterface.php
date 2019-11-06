<?php

namespace AnyKey\MobilePaymentsBundle\Interfaces\Parser;

use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;

interface MultiplePurchaseReceiptsInterface
{
    /**
     * @return PurchaseReceiptInterface[]
     */
    public function create(): array;
}