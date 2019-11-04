<?php


namespace AnyKey\MobilePaymentsBundle\Data\Receipt;


use AnyKey\MobilePaymentsBundle\Interfaces\ReceiptDataInterface;

/**
 * Class AppleReceiptData
 * @package AnyKey\MobilePaymentsBundle\Data\Receipt
 */
class AppleReceiptData implements ReceiptDataInterface
{
    /** @var string */
    private $receipt;
    /** @var array */
    private $options = [];

    public function __construct(string $receipt, bool $excludeOld = false)
    {
        $this->receipt = $receipt;
        $this->options['exclude_old'] = $excludeOld;
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
        return $this->options;
    }

    public function isExcludeOld(): bool
    {
        return $this->options['exclude_old'];
    }
}