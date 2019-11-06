<?php


namespace AnyKey\MobilePaymentsBundle\Interfaces\Parser;


interface ReceiptGeneratorInterface
{
    /**
     * Set a raw response for the generators to parse data from
     *
     * @param string $rawResponse
     * @return ReceiptGeneratorInterface
     */
    public function init(string $rawResponse): self;

    /**
     * Generate one-time purchases
     * @return \Generator
     */
    public function generatePurchases();

    /**
     * Generate subscriptions. Subscriptions are sorted by newest.
     * @return \Generator
     */
    public function generateSubscriptions();
}