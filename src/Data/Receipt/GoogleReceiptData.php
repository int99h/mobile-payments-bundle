<?php


namespace AnyKey\Data\Receipt;


use AnyKey\Interfaces\ReceiptDataInterface;

class GoogleReceiptData implements ReceiptDataInterface
{
    /** @var string */
    private $receipt;

    public function __construct(string $receipt)
    {
        $this->receipt = $receipt;
    }

    /**
     * Set encoded receipt data
     * @return string
     */
    public function getReceipt(): string
    {
        return $this->receipt;
    }

    /**
     * Get receipt special options
     * @return array
     */
    public function getOptions(): array
    {
        return [];
    }
}