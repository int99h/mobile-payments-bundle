<?php


namespace AnyKey\MobilePaymentsBundle\Interfaces\Parser;


interface ReceiptGeneratorInterface
{
    /**
     * Set a raw response for the generators to extract data from
     *
     * @param string $rawResponse
     * @return ReceiptGeneratorInterface
     */
    public function init(string $rawResponse): self;

    /**
     * Generate one-time product purchase items
     * @return \Generator
     */
    public function generatePurchases();

    /**
     * Generate subscription purchase items. Sorted by the latest purchase date.
     * @return \Generator
     */
    public function generateSubscriptions();
}