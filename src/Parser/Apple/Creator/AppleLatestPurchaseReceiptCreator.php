<?php


namespace AnyKey\MobilePaymentsBundle\Parser\Apple\Creator;

use AnyKey\MobilePaymentsBundle\Factory\AppleReceiptFactory;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\AppleReceiptParserInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;

class AppleLatestPurchaseReceiptCreator
{
    /**
     * @var AppleReceiptParserInterface
     */
    private $appleReceiptParser;
    /**
     * @var bool
     */
    private $isSandbox;

    /**
     * AppleLatestPurchaseReceipt constructor.
     * @param AppleReceiptParserInterface $appleReceiptParser
     * @param bool $isSandbox
     */
    public function __construct(AppleReceiptParserInterface $appleReceiptParser, bool $isSandbox = false)
    {
        $this->appleReceiptParser = $appleReceiptParser;
        $this->isSandbox = $isSandbox;
    }

    /**
     * @return PurchaseReceiptInterface|null
     */
    public function create(): ?PurchaseReceiptInterface
    {
        foreach ($this->appleReceiptParser->parsePurchases() as $purchaseItem) {
            return AppleReceiptFactory::createPurchaseFromParsedData(
                $purchaseItem,
                $this->appleReceiptParser->parseRefreshPayload(),
                $this->isSandbox
            );
        }

        return null;
    }
}