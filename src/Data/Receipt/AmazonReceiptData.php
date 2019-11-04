<?php


namespace AnyKey\MobilePaymentsBundle\Data\Receipt;


use AnyKey\MobilePaymentsBundle\Interfaces\ReceiptDataInterface;

class AmazonReceiptData implements ReceiptDataInterface
{
    /** @var string */
    private $receiptId;
    /** @var array */
    private $options = [];

    public function __construct(string $receiptId, string $userId)
    {
        $this->receiptId = $receiptId;
        $this->options['user_id'] = $userId;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->options['user_id'];
    }

    /**
     * Set encoded receipt data
     * @return string
     */
    public function getReceipt(): string
    {
        return $this->receiptId;
    }

    /**
     * Get receipt special options
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}