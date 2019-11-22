<?php


namespace AnyKey\MobilePaymentsBundle\Data\Receipt;


use AnyKey\MobilePaymentsBundle\Interfaces\Parser\ReceiptGeneratorInterface;
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
    /** @var ReceiptGeneratorInterface|null */
    private $receiptGenerator;

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

    /**
     * @return ReceiptGeneratorInterface|null
     */
    public function getReceiptGenerator(): ?ReceiptGeneratorInterface
    {
        return $this->receiptGenerator;
    }

    /**
     * @param ReceiptGeneratorInterface|null $receiptGenerator
     * @return AppleReceiptData
     */
    public function setReceiptGenerator(?ReceiptGeneratorInterface $receiptGenerator): self
    {
        $this->receiptGenerator = $receiptGenerator;
        return $this;
    }
}