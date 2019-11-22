<?php


namespace AnyKey\MobilePaymentsBundle\Interfaces;


interface ReceiptDataInterface
{
    /**
     * Set encoded receipt data
     * @return string
     */
    public function getReceipt(): string;

    /**
     * Get receipt special options
     * @return array
     */
    public function getOptions(): array;
}