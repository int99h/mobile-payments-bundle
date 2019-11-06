<?php


namespace AnyKey\MobilePaymentsBundle\Parser\Apple\Composer;

use AnyKey\MobilePaymentsBundle\Factory\ApplePurchaseReceiptFactory;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\AppleReceiptParserInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\MultiplePurchaseReceiptsInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;

final class ApplePurchaseReceiptsCollection implements MultiplePurchaseReceiptsInterface
{
    /**
     * @var AppleReceiptParserInterface
     */
    private $applePaymentParser;
    /**
     * @var bool
     */
    private $isSandbox;

    /**
     * ApplePurchaseProductReceiptsCollection constructor.
     * @param AppleReceiptParserInterface $applePaymentParser
     * @param bool $isSandbox
     */
    public function __construct(AppleReceiptParserInterface $applePaymentParser, bool $isSandbox = false)
    {
        $this->applePaymentParser = $applePaymentParser;
        $this->isSandbox = $isSandbox;
    }

    /**
     * @return PurchaseReceiptInterface[]
     */
    public function render(): array
    {
        $purchaseProductReceipts = [];

        foreach ($this->applePaymentParser->parsePurchases() as $purchaseItem) {
            $purchaseProductReceipts[] = ApplePurchaseReceiptFactory::createFromParsedData(
                $purchaseItem,
                $this->applePaymentParser->parseRefreshPayload(),
                $this->isSandbox
            );
        }

        return $purchaseProductReceipts;
    }
}