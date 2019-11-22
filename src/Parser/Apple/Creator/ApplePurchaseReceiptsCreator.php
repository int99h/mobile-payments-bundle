<?php


namespace AnyKey\Parser\Apple\Creator;

use AnyKey\Factory\AppleReceiptFactory;
use AnyKey\Interfaces\Parser\AppleReceiptParserInterface;
use AnyKey\Interfaces\PurchaseReceiptInterface;

class ApplePurchaseReceiptsCreator
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
     * ApplePurchaseReceiptsCreator constructor.
     * @param AppleReceiptParserInterface $appleReceiptParser
     * @param bool $isSandbox
     */
    public function __construct(AppleReceiptParserInterface $appleReceiptParser, bool $isSandbox = false)
    {
        $this->appleReceiptParser = $appleReceiptParser;
        $this->isSandbox = $isSandbox;
    }

    /**
     * @return PurchaseReceiptInterface[]
     */
    public function create(): array
    {
        $purchaseReceipts = [];

        foreach ($this->appleReceiptParser->parsePurchases() as $purchaseItem) {
            $purchaseReceipts[] = AppleReceiptFactory::createPurchaseFromParsedData(
                $purchaseItem,
                $this->appleReceiptParser->parseRefreshPayload(),
                $this->isSandbox
            );
        }

        return $purchaseReceipts;
    }
}