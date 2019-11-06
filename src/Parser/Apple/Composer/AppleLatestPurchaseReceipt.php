<?php


namespace AnyKey\MobilePaymentsBundle\Parser\Apple\Composer;

use AnyKey\MobilePaymentsBundle\Factory\ApplePurchaseReceiptFactory;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\AppleReceiptParserInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\SinglePurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;

final class AppleLatestPurchaseReceipt implements SinglePurchaseReceiptInterface
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
     * AppleLatestPurchaseProductReceiptComposer constructor.
     * @param AppleReceiptParserInterface $applePaymentParser
     * @param bool $isSandbox
     */
    public function __construct(AppleReceiptParserInterface $applePaymentParser, bool $isSandbox = false)
    {
        $this->applePaymentParser = $applePaymentParser;
        $this->isSandbox = $isSandbox;
    }

    /**
     * @return PurchaseReceiptInterface|null
     */
    public function create(): ?PurchaseReceiptInterface
    {
        foreach ($this->applePaymentParser->parsePurchaseProducts() as $purchaseItem) {
            return ApplePurchaseReceiptFactory::createFromParsedData(
                $purchaseItem,
                $this->applePaymentParser->parseRefreshPayload(),
                $this->isSandbox
            );
        }

        return null;
    }
}