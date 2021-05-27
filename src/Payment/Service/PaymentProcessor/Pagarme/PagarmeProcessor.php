<?php

namespace App\Payment\Service\PaymentProcessor\Pagarme;

use App\Core\Exception\InvalidInputException;
use App\Payment\Dto\PagarmeTransactionInputDto;
use App\Payment\Entity\Payment;
use App\Payment\Message\PagarmeSubscriptionResponseReceivedEvent;
use App\Payment\Message\PagarmeTransactionResponseReceivedEvent;
use App\Vendor\Entity\VendorPlan;
use App\Vendor\Exception\VendorPlanInvalidDurationException;
use App\Vendor\Service\VendorSettingManager;
use Decimal\Decimal;
use PagarMe\Client;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class PagarmeProcessor
{
    private VendorSettingManager $vendorSettingManager;

    private Client $pagarmeClient;

    private MessageBusInterface $messageBus;

    private string $pagarmePostbackUrl;

    private string $pagarmeRecebedorId;

    public function __construct(
        VendorSettingManager $vendorSettingManager,
        Client $pagarmeClient,
        MessageBusInterface $messageBus,
        string $pagarmePostbackUrl,
        string $pagarmeRecebedorId
    ) {
        $this->vendorSettingManager = $vendorSettingManager;
        $this->pagarmeClient = $pagarmeClient;
        $this->messageBus = $messageBus;
        $this->pagarmePostbackUrl = $pagarmePostbackUrl;
        $this->pagarmeRecebedorId = $pagarmeRecebedorId;
    }

    public function process(Payment $payment)
    {
        $this->validate($payment);

        $transactionInput = $this->prepareTransactionInput($payment);

        $transactionData = $this->prepareTransactionData($transactionInput);

        $this->appendPostbackInformation($transactionData, $transactionInput);
        $this->appendCustomerInformation($transactionData, $transactionInput);
        $this->appendBillingInformation($transactionData, $transactionInput);
        $this->appendItems($transactionData, $transactionInput);
        $this->appendSplitRules($transactionData, $transactionInput);

        if ($transactionInput->isRecurring) {
            $transactionData['plan_id'] = $transactionInput->pagarmePlanId;

            $response = $this->pagarmeClient->subscriptions()->create($transactionData);

            $this->messageBus->dispatch(
                new PagarmeSubscriptionResponseReceivedEvent(
                    $response,
                    $transactionInput->subscriptionId,
                    $transactionInput->paymentId
                )
            );
        } else {
            $response = $this->pagarmeClient->transactions()->create($transactionData);

            $this->messageBus->dispatch(
                new PagarmeTransactionResponseReceivedEvent($response, $transactionInput->paymentId)
            );
        }
    }

    public function prepareTransactionInput(Payment $payment)
    {
        $customer = $payment->getInvoice()->getSubscription()->getCustomer();
        $vendorPlan = $payment->getInvoice()->getSubscription()->getVendorPlan();

        $transactionInput = new PagarmeTransactionInputDto();
        $transactionInput->subscriptionId = $payment->getInvoice()->getSubscription()->getId()->toString();
        $transactionInput->paymentId = $payment->getId()->toString();
        $transactionInput->vendorId = $vendorPlan->getVendor()->getId()->toString();
        $transactionInput->vendorPlanId = $vendorPlan->getId()->toString();
        $transactionInput->invoiceId = $payment->getInvoiceId()->toString();
        $transactionInput->customerId = $customer->getId()->toString();
        $transactionInput->customerName = $customer->getName();
        $transactionInput->customerEmail = $customer->getEmail();
        $transactionInput->customerPhone = $customer->getPhone();
        $transactionInput->customerDocumentType = 'cpf';
        $transactionInput->customerDocumentNumber = $customer->getDocument('cpf');
        $transactionInput->amount = new Decimal($payment->getInvoice()->getTotalAmount());
        $transactionInput->productId = $vendorPlan->getId()->toString();
        $transactionInput->productName = $vendorPlan->getName();
        $transactionInput->isRecurring = $vendorPlan->isRecurring();

        if ($transactionInput->isRecurring) {
            $transactionInput->pagarmePlanId = $this->createPlan($vendorPlan);
        }

        return $transactionInput;
    }

    protected function appendCustomerInformation(array &$transactionData, PagarmeTransactionInputDto $transactionInput)
    {
        $transactionData['customer'] = [
            'external_id' => $transactionInput->customerId,
            'name' => $transactionInput->customerName,
            'type' => 'individual',
            'country' => 'br',
            'documents' => [
                [
                    'type' => $transactionInput->customerDocumentType,
                    'number' => $transactionInput->customerDocumentNumber,
                ],
            ],
            'phone_numbers' => [
                $transactionInput->customerPhone ?: '+5511989737737',
            ],
            'email' => $transactionInput->customerEmail,
        ];
    }

    protected function appendBillingInformation(array &$transactionData, PagarmeTransactionInputDto $transactionInput)
    {
        $transactionData['billing'] = [
            'name' => $transactionInput->customerName,
            'address' => [
                'country' => 'br',
                'street' => 'Avenida Brigadeiro Faria Lima',
                'street_number' => '1811',
                'state' => 'sp',
                'city' => 'Sao Paulo',
                'neighborhood' => 'Jardim Paulistano',
                'zipcode' => '01451001',
            ],
        ];
    }

    protected function appendItems(array &$transactionData, PagarmeTransactionInputDto $transactionInput)
    {
        $transactionData['amount'] = $transactionInput->amount->mul(100)->toFixed(0);
        $transactionData['items'] = [
            [
                'id' => $transactionInput->productId,
                'title' => $transactionInput->productName,
                'unit_price' => $transactionInput->amount->mul(100)->toFixed(0),
                'quantity' => 1,
                'tangible' => false,
            ],
        ];
    }

    protected function appendSplitRules(array &$transactionData, PagarmeTransactionInputDto $transactionInput)
    {
        $platformPagarmeId = $this->pagarmeRecebedorId;
        $vendorPagarmeId = $this->vendorSettingManager->getValue(
            Uuid::fromString($transactionInput->vendorId),
            'pagarme_id'
        );

        $transactionData['split_rules'] = [
            [
                'recipient_id' => $platformPagarmeId,
                'liable' => false,
                'charge_processing_fee' => true,
                'percentage' => 10,
            ],
            [
                'recipient_id' => $vendorPagarmeId,
                'liable' => true,
                'charge_processing_fee' => false,
                'percentage' => 90,
            ],
        ];
    }

    protected function appendPostbackInformation(array &$transactionData, PagarmeTransactionInputDto $transactionInput)
    {
        $postbackUrl = $this->pagarmePostbackUrl;
        $postbackUrl .= '?reference='.$transactionInput->paymentId;

        $transactionData['postback_url'] = $postbackUrl;
    }

    protected function validate(Payment $payment)
    {
        $customer = $payment->getInvoice()->getSubscription()->getCustomer();

        if (null === $customer->getDocument('cpf')) {
            throw new InvalidInputException('Missing customer CPF');
        }
    }

    protected function createPlan(VendorPlan $vendorPlan): int
    {
        if (0 !== $vendorPlan->getDuration()->d) {
            $days = $vendorPlan->getDuration()->d;
        } elseif (0 !== $vendorPlan->getDuration()->m) {
            $days = $vendorPlan->getDuration()->m * 30;
        } elseif (0 !== $vendorPlan->getDuration()->y) {
            $days = $vendorPlan->getDuration()->y * 365;
        } else {
            throw new VendorPlanInvalidDurationException();
        }

        $plan = $this->pagarmeClient->plans()->create([
            'amount' => $vendorPlan->getPrice()->mul(100)->toFixed(0),
            'days' => $days,
            'name' => $vendorPlan->getName(),
        ]);

        return $plan->id;
    }

    abstract protected function prepareTransactionData(PagarmeTransactionInputDto $transactionInput): array;
}
