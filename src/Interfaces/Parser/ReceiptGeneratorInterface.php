<?php


namespace AnyKey\Interfaces\Parser;


use AnyKey\Exception\RuntimeException;

interface ReceiptGeneratorInterface
{
    /**
     * Generate one-time product purchase items
     * @return \Generator
     * @throws RuntimeException
     */
    public function generatePurchases();

    /**
     * Generate subscription purchase items. Sorted by the latest purchase date.
     * @return \Generator
     */
    public function generateSubscriptions();
}