<?php

namespace App\Payment\Service\PaymentProcessor\Pagarme;

use App\Core\Exception\InvalidInputException;
use App\Payment\Dto\PagarmeTransactionInputDto;
use App\Payment\Entity\Payment;
use App\Payment\Message\PagarmeTransactionResponseReceivedEvent;
use App\Vendor\Entity\Vendor;
use App\Vendor\Service\VendorSettingManager;
use Decimal\Decimal;
use PagarMe\Client;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class PagarmeProcessor
{
    private VendorSettingManager $vendorSettingManager;

    private Client $pagarmeClient;

    private MessageBusInterface $messageBus;

    public function __construct(
        VendorSettingManager $vendorSettingManager,
        Client $pagarmeClient,
        MessageBusInterface $messageBus
    ) {
        $this->vendorSettingManager = $vendorSettingManager;
        $this->pagarmeClient = $pagarmeClient;
        $this->messageBus = $messageBus;
    }

    public function process(Payment $payment)
    {
        $this->validate($payment);

        $transactionInput = $this->prepareTransactionInput($payment);

        $transactionData = $this->prepareTransactionData($transactionInput);

        $this->appendSplitRules(
            $transactionData,
            $payment->getInvoice()->getSubscription()->getVendorPlan()->getVendor()->getId()
        );

        $response = $this->pagarmeClient->transactions()->create($transactionData);

        $this->messageBus->dispatch(
            new PagarmeTransactionResponseReceivedEvent($payment->getId(), $response)
        );
    }

    public function prepareTransactionInput(Payment $payment)
    {
        $customer = $payment->getInvoice()->getSubscription()->getCustomer();
        $vendorPlan = $payment->getInvoice()->getSubscription()->getVendorPlan();

        $transactionInput = new PagarmeTransactionInputDto();
        $transactionInput->customerId = $customer->getId()->toString();
        $transactionInput->customerName = $customer->getName();
        $transactionInput->customerEmail = $customer->getEmail();
        $transactionInput->customerPhone = $customer->getPhone();
        $transactionInput->customerDocumentType = 'cpf';
        $transactionInput->customerDocumentNumber = $customer->getDocument('cpf');
        $transactionInput->amount = new Decimal($payment->getInvoice()->getTotalAmount());
        $transactionInput->productId = $vendorPlan->getId()->toString();
        $transactionInput->productName = $vendorPlan->getName();

        return $transactionInput;
    }

    protected function appendSplitRules(array &$transactionData, UuidInterface $vendorId)
    {
        $platformPagarmeId = 're_cklii1riy0nqr0h9tq3mvq3ve';
        $vendorPagarmeId = $this->vendorSettingManager->getValue($vendorId, 'pagarme_id');

        $transactionData['split_rules'] = [
            [
                'recipient_id' => $platformPagarmeId,
                'liable' => false,
                'charge_processing_fee' => true,
                'percentage' => 10
            ],
            [
                'recipient_id' => $vendorPagarmeId,
                'liable' => true,
                'charge_processing_fee' => false,
                'percentage' => 90,
            ],
        ];
    }

    protected function validate(Payment $payment)
    {
        $customer = $payment->getInvoice()->getSubscription()->getCustomer();

        if (null === $customer->getDocument('cpf')) {
            throw new InvalidInputException("Missing customer CPF");
        }
    }

    abstract protected function prepareTransactionData(PagarmeTransactionInputDto $transactionInput): array;
}