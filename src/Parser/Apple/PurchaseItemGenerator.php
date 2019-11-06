<?php


namespace AnyKey\MobilePaymentsBundle\Parser\Apple;

use AnyKey\MobilePaymentsBundle\Interfaces\Parser\PurchaseItemGeneratorInterface;
use Peekmo\JsonPath\JsonStore;
use ReceiptValidator\iTunes\PurchaseItem;

class PurchaseItemGenerator implements PurchaseItemGeneratorInterface
{
    /**
     * @var string
     */
    private $rawResponse;

    /**
     * Set a raw response for the generators to extract data from
     *
     * @param string $rawResponse
     * @return PurchaseItemGeneratorInterface
     */
    public function init(string $rawResponse): PurchaseItemGeneratorInterface
    {
        $this->rawResponse = $rawResponse;

        return $this;
    }

    /**
     * Generate one-time product purchase items
     * @return \Generator
     * @throws \ReceiptValidator\RunTimeException
     */
    public function generateProductPurchaseItems()
    {
        $purchaseProducts = @(new JsonStore($this->rawResponse))
            ->get('$.latest_receipt_info[?(@.expires_date==null)]');

        foreach ($purchaseProducts as $purchaseProduct) {
            yield new PurchaseItem($purchaseProduct);
        }
    }

    /**
     * Generate subscription purchase items. Sorted by the latest purchase date.
     * @return \Generator
     */
    public function generateSubscriptionPurchaseItems()
    {
        $subscriptionProducts = @(new JsonStore($this->rawResponse))
            ->get('$.latest_receipt_info[?(@.expires_date!=null)]');

        $subscriptionProducts = array_map(function ($data) {
            return new PurchaseItem($data);
        }, $subscriptionProducts);
        usort($subscriptionProducts, function (PurchaseItem $a, PurchaseItem $b) {
            return $b->getPurchaseDate()->getTimestamp() - $a->getPurchaseDate()->getTimestamp();
        });

        /** @var PurchaseItem $purchaseProduct */
        foreach ($subscriptionProducts as $purchaseProduct) {
            yield $purchaseProduct;
        }
    }
}