<?php

namespace AnyKey\Data\Composer;

use AnyKey\Exception\ReceiptParserException;
use AnyKey\Interfaces\Parser\ReceiptGeneratorInterface;
use AnyKey\Interfaces\PurchaseReceiptInterface;
use AnyKey\Interfaces\ReceiptComposerInterface;
use AnyKey\Interfaces\SubscriptionReceiptInterface;
use AnyKey\Data\PurchaseReceipt;
use AnyKey\Data\SubscriptionReceipt;
use AnyKey\Parser\Apple\Creator\AppleLatestPurchaseReceiptCreator;
use AnyKey\Parser\Apple\Creator\AppleLatestSubscriptionReceiptCreator;
use AnyKey\Parser\AppleReceiptParser;
use AnyKey\Data\Validator\iTunes\ResponseInterface;

/**
 * Class AppleReceiptComposer
 * @package AnyKey\Data\Creator
 */
class AppleReceiptComposer implements ReceiptComposerInterface
{
    /** @var ResponseInterface */
    private $response;
    /**
     * @var ReceiptGeneratorInterface|null
     */
    private $receiptGenerator;

    /**
     * AppleReceiptComposer constructor.
     * @param ResponseInterface $response
     * @param ReceiptGeneratorInterface $receiptGenerator
     */
    public function __construct(ResponseInterface $response, ReceiptGeneratorInterface $receiptGenerator = null)
    {
        $this->response = $response;
        $this->receiptGenerator = $receiptGenerator;
    }

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     * @return PurchaseReceiptInterface
     * @throws ReceiptParserException
     */
    public function purchase(): PurchaseReceiptInterface
    {
        $receipt = (new AppleLatestPurchaseReceiptCreator(
            new AppleReceiptParser($this->response, $this->receiptGenerator),
            $this->response->isSandbox()
        ))->create();

        if (!$receipt) {
            throw new ReceiptParserException('Failed to parse Apple purchase receipt');
        }

        if ($receipt instanceof PurchaseReceipt) {
            $receipt->setOriginalResponse($this->response);
        }

        return $receipt;
    }

    /**
     * Compose a purchase receipt that fits providers' validation criteria
     * @return SubscriptionReceiptInterface
     * @throws ReceiptParserException
     */
    public function subscription(): SubscriptionReceiptInterface
    {
        $receipt = (new AppleLatestSubscriptionReceiptCreator(
            new AppleReceiptParser($this->response, $this->receiptGenerator),
            $this->response->isSandbox()
        ))->create();

        if (!$receipt) {
            throw new ReceiptParserException('Failed to parse Apple subscription receipt');
        }

        if ($receipt instanceof SubscriptionReceipt) {
            $receipt->setOriginalResponse($this->response);
        }

        return $receipt;
    }
}