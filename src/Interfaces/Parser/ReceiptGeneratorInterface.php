<?php


namespace AnyKey\MobilePaymentsBundle\Interfaces\Parser;


interface ReceiptGeneratorInterface
{
    /**
     * Generate one-time product purchase items
     * @return \Generator
     * @throws \ReceiptValidator\RunTimeException
     */
    public function generatePurchases();

    /**
     * Generate subscription purchase items. Sorted by the latest purchase date.
     * @return \Generator
     */
    public function generateSubscriptions();
}