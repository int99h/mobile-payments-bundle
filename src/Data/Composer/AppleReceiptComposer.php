<?php

namespace AnyKey\MobilePaymentsBundle\Data\Composer;

use AnyKey\MobilePaymentsBundle\Exception\ReceiptParserException;
use AnyKey\MobilePaymentsBundle\Interfaces\Parser\ReceiptGeneratorInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\PurchaseReceiptInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\ReceiptComposerInterface;
use AnyKey\MobilePaymentsBundle\Interfaces\SubscriptionReceiptInterface;
use AnyKey\MobilePaymentsBundle\Data\PurchaseReceipt;
use AnyKey\MobilePaymentsBundle\Data\SubscriptionReceipt;
use AnyKey\MobilePaymentsBundle\Parser\Apple\Creator\AppleLatestPurchaseReceiptCreator;
use AnyKey\MobilePaymentsBundle\Parser\Apple\Creator\AppleLatestSubscriptionReceiptCreator;
use AnyKey\MobilePaymentsBundle\Parser\AppleReceiptParser;
use Data\Validator\iTunes\ResponseInterface;

/**
 * Class AppleReceiptComposer
 * @package AnyKey\MobilePaymentsBundle\Data\Creator
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