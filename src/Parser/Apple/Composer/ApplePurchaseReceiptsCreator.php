<?php


namespace AnyKey\MobilePaymentsBundle\Parser\Apple\Composer;

use AnyKey\MobilePaymentsBundle\Factory\ApplePurchaseReceiptFactory;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\AppleReceiptParserInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\MultiplePurchaseReceiptsInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;

final class ApplePurchaseReceiptsCreator implements MultiplePurchaseReceiptsInterface
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
            $purchaseReceipts[] = ApplePurchaseReceiptFactory::createFromParsedData(
                $purchaseItem,
                $this->appleReceiptParser->parseRefreshPayload(),
                $this->isSandbox
            );
        }

        return $purchaseReceipts;
    }
}