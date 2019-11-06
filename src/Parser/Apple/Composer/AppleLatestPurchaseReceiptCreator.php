<?php


namespace AnyKey\MobilePaymentsBundle\Parser\Apple\Composer;

use AnyKey\MobilePaymentsBundle\Factory\ApplePurchaseReceiptFactory;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\AppleReceiptParserInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\SinglePurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;

final class AppleLatestPurchaseReceiptCreator implements SinglePurchaseReceiptInterface
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
            return ApplePurchaseReceiptFactory::createFromParsedData(
                $purchaseItem,
                $this->appleReceiptParser->parseRefreshPayload(),
                $this->isSandbox
            );
        }

        return null;
    }
}