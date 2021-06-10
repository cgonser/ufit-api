<?php

namespace App\Payment\Service\PaymentProcessor\Pagarme;

use App\Core\Exception\InvalidInputException;
use App\Payment\Dto\PagarmeTransactionInputDto;
use App\Payment\Entity\Payment;
use App\Payment\Exception\MissingVendorBankAccountException;
use App\Payment\Exception\PagarmeInvalidInputException;
use App\Payment\Message\PagarmeSubscriptionResponseReceivedEvent;
use App\Payment\Message\PagarmeTransactionResponseReceivedEvent;
use App\Vendor\Entity\VendorPlan;
use App\Vendor\Exception\VendorPlanInvalidDurationException;
use App\Vendor\Service\VendorSettingManager;
use Decimal\Decimal;
use PagarMe\Client;
use PagarMe\Exceptions\PagarMeException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class PagarmeProcessor
{
    const RESPONSE_TIMEOUT = 5;

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

            try {
                $response = $this->refreshResponse(
                    $this->pagarmeClient->subscriptions()->create($transactionData),
                    'subscription'
                );
            } catch (PagarMeException $e) {
                $this->handlePagarmeSusbcriptionException(
                    $payment,
                    $e
                );
            }

            $this->messageBus->dispatch(
                new PagarmeSubscriptionResponseReceivedEvent(
                    $response,
                    $transactionInput->subscriptionId,
                    $transactionInput->paymentId
                )
            );
        } else {
            try {
                $response = $this->refreshResponse(
                    $this->pagarmeClient->transactions()->create($transactionData),
                    'transaction'
                );
            } catch (PagarMeException $e) {
                $this->handlePagarmeTransactionException($e);
            }

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
        $transactionInput->customerPhoneIntlCode = $customer->getPhoneIntlCode();
        $transactionInput->customerPhoneAreaCode = $customer->getPhoneAreaCode();
        $transactionInput->customerPhoneNumber = $customer->getPhoneNumber();
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
            'email' => $transactionInput->customerEmail,
        ];

        if ($transactionInput->isRecurring) {
            $transactionData['customer']['address'] = [
                'country' => 'br',
                'street' => 'Avenida Damasceno Vieira',
                'street_number' => '900',
                'state' => 'sp',
                'city' => 'Sao Paulo',
                'neighborhood' => 'Vila Mascote',
                'zipcode' => '04363040',
            ];
            $transactionData['customer']['phone'] = [
                'ddd' => ltrim($transactionInput->customerPhoneAreaCode, '0'),
                'number' => $transactionInput->customerPhoneNumber,
            ];
        } else {
            $phoneNumber = $transactionInput->customerPhoneNumber ?: '+55'.
                    ltrim($transactionInput->customerPhoneAreaCode, '0').
                    $transactionInput->customerPhoneNumber;

            if (0 !== strpos($phoneNumber, '+')) {
                $phoneNumber = '+' . $phoneNumber;
            }

            $transactionData['customer']['phone_numbers'] = [
                $phoneNumber
            ];
        }
    }

    protected function appendBillingInformation(array &$transactionData, PagarmeTransactionInputDto $transactionInput)
    {
        $transactionData['billing'] = [
            'name' => $transactionInput->customerName,
            'address' => [
                'country' => 'br',
                'street' => 'Avenida Damasceno Vieira',
                'street_number' => '900',
                'state' => 'sp',
                'city' => 'Sao Paulo',
                'neighborhood' => 'Vila Mascote',
                'zipcode' => '04363040',
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
        } elseif (12 === $vendorPlan->getDuration()->m) {
            $days = 365;
        } elseif (0 !== $vendorPlan->getDuration()->m) {
            $days = $vendorPlan->getDuration()->m * 30;
        } elseif (0 !== $vendorPlan->getDuration()->y) {
            $days = $vendorPlan->getDuration()->y * 365;
        } else {
            throw new VendorPlanInvalidDurationException();
        }

        try {
            $plan = $this->pagarmeClient->plans()->create([
                'amount' => $vendorPlan->getPrice()->mul(100)->toFixed(0),
                'days' => $days,
                'name' => $vendorPlan->getName(),
            ]);
        } catch (PagarMeException $e) {
            $this->handlePagarmeVendorPlanException($vendorPlan, $e);
        }

        return $plan->id;
    }

    private function refreshResponse(\stdClass $response, string $type, int $timeout = self::RESPONSE_TIMEOUT): \stdClass
    {
        $start = time();

        while ('processing' === $response->status) {
            if (time() - $start >= $timeout) {
                break;
            }

            if ('subscription' === $type) {
                $response = $this->pagarmeClient->subscriptions()->get(['id' => $response->id]);
            } else {
                $response = $this->pagarmeClient->transactions()->get(['id' => $response->id]);
            }
        }

        return $response;
    }

    private function handlePagarmeVendorPlanException(VendorPlan $vendorPlan, PagarMeException $e): void
    {
        $propertyName = $e->getParameterName();

        throw new PagarmeInvalidInputException($vendorPlan, $propertyName, $e->getMessage());
    }

    abstract protected function prepareTransactionData(PagarmeTransactionInputDto $transactionInput): array;

    private function handlePagarmeSusbcriptionException(Payment $payment, \Exception $e)
    {
        $subscription = $payment->getInvoice()->getSubscription();

        $customerProperties = [
            'customer[phone][ddd]' => 'phoneAreaCode',
            'customer[phone][number]' => 'phoneNumber',
        ];

        if (isset($customerProperties[$e->getParameterName()])) {
            $propertyName = $customerProperties[$e->getParameterName()] ?? $e->getParameterName();

            throw new PagarmeInvalidInputException($subscription->getCustomer(), $propertyName, $e->getMessage());
        }

        if ('split_rules[1][recipient_id]' === $e->getParameterName()) {
            throw new MissingVendorBankAccountException($e->getMessage());
        }

        $vendorProperties = [
        ];

        if (isset($vendorProperties[$e->getParameterName()])) {
            $propertyName = $vendorProperties[$e->getParameterName()] ?? $e->getParameterName();

            throw new PagarmeInvalidInputException($subscription->getVendorPlan()->getVendor(), $propertyName, $e->getMessage());
        }

        $paymentProperties = [
            'card_hash' => 'details',
        ];

        if (isset($paymentProperties[$e->getParameterName()])) {
            $propertyName = $paymentProperties[$e->getParameterName()] ?? $e->getParameterName();

            throw new PagarmeInvalidInputException($payment, $propertyName, $e->getMessage());
        }

        throw new PagarmeInvalidInputException(new \StdClass(), $e->getParameterName(), $e->getMessage());
    }

    private function handlePagarmeTransactionException(\Exception $e)
    {
        throw new PagarmeInvalidInputException(new \StdClass(), $e->getParameterName(), $e->getMessage());
    }
}
