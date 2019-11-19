<?php


namespace AnyKey\MobilePaymentsBundle\Parser\Apple\Creator;

use AnyKey\MobilePaymentsBundle\Factory\AppleReceiptFactory;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\AppleReceiptParserInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;

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