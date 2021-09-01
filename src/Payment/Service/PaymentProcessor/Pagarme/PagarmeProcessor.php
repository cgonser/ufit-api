<?php

declare(strict_types=1);

namespace App\Payment\Service\PaymentProcessor\Pagarme;

use App\Core\Exception\InvalidInputException;
use App\Customer\Entity\BillingInformation;
use App\Customer\Exception\BillingInformationNotFoundException;
use App\Payment\Entity\Payment;
use App\Payment\Exception\MissingVendorBankAccountException;
use App\Payment\Exception\PagarmeInvalidInputException;
use App\Payment\Message\PagarmeSubscriptionResponseReceivedEvent;
use App\Payment\Message\PagarmeTransactionResponseReceivedEvent;
use App\Subscription\Service\SubscriptionManager;
use App\Vendor\Entity\VendorPlan;
use App\Vendor\Exception\VendorPlanInvalidDurationException;
use App\Vendor\Service\VendorSettingManager;
use Decimal\Decimal;
use PagarMe\Client;
use PagarMe\Exceptions\PagarMeException;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class PagarmeProcessor
{
    /**
     * @var int
     */
    public const RESPONSE_TIMEOUT = 5;

    public function __construct(
        private VendorSettingManager $vendorSettingManager,
        private SubscriptionManager $subscriptionManager,
        private Client $pagarmeClient,
        private MessageBusInterface $messageBus,
        private string $pagarmePostbackUrl,
        private string $pagarmeRecebedorId,
    ) {
    }

    public function process(Payment $payment)
    {
        $this->validate($payment);

        $subscription = $payment->getInvoice()->getSubscription();
        $this->subscriptionManager->defineExternalRefence($subscription, null);

        $vendorPlan = $payment->getInvoice()->getSubscription()->getVendorPlan();

        $transactionData = $this->prepareTransactionData($payment);

        $this->appendPostbackInformation($transactionData, $payment);
        $this->appendCustomerInformation($transactionData, $payment);
        $this->appendBillingInformation($transactionData, $payment);
        $this->appendItems($transactionData, $payment);
        $this->appendSplitRules($transactionData, $payment);

        if ($vendorPlan->isRecurring()) {
            $transactionData['plan_id'] = $this->createPlan($vendorPlan);

            try {
                $response = $this->refreshResponse(
                    $this->pagarmeClient->subscriptions()->create($transactionData),
                    'subscription'
                );
            } catch (PagarMeException $pagarMeException) {
                $this->handlePagarmeSusbcriptionException($payment, $pagarMeException);
            }

            $this->messageBus->dispatch(
                new PagarmeSubscriptionResponseReceivedEvent(
                    $response,
                    $subscription->getId(),
                    $payment->getId()
                )
            );
        } else {
            try {
                $response = $this->refreshResponse(
                    $this->pagarmeClient->transactions()->create($transactionData),
                    'transaction'
                );
            } catch (PagarMeException $pagarMeException) {
                $this->handlePagarmeTransactionException($pagarMeException);
            }

            $this->messageBus->dispatch(
                new PagarmeTransactionResponseReceivedEvent(
                    $response,
                    $subscription->getId(),
                    $payment->getId()
                )
            );
        }
    }

    protected function appendCustomerInformation(array &$transactionData, Payment $payment): void
    {
        $billingInformation = $payment->getBillingInformation();
        $customer = $payment->getInvoice()->getSubscription()->getCustomer();

        $phoneAreaCode = $customer->getPhoneAreaCode() ?: $billingInformation->getPhoneAreaCode();
        $phoneNumber = $customer->getPhoneNumber() ?: $billingInformation->getPhoneNumber();

        $transactionData['customer'] = [
            'external_id' => $customer->getId()->toString(),
            'name' => $billingInformation->getName() ?: $customer->getName(),
            'type' => 'individual',
            'country' => 'br',
            'documents' => [
                [
                    'type' => 'cpf',
                    'number' => $billingInformation->getDocumentNumber(),
                ],
            ],
            'email' => $billingInformation->getEmail() ?: $customer->getEmail(),
        ];

        if ($payment->getInvoice()->getSubscription()->getVendorPlan()->isRecurring()) {
            $transactionData['customer']['address'] = $this->prepareAddressData($billingInformation);
            $transactionData['customer']['phone'] = [
                'ddd' => ltrim($phoneAreaCode ?? '', '0'),
                'number' => $phoneNumber,
            ];
        } else {
            $phoneNumber = $phoneNumber ?: '+55'.
                ltrim($phoneAreaCode ?? '', '0').
                $phoneNumber;

            if (!str_starts_with($phoneNumber, '+')) {
                $phoneNumber = '+'.$phoneNumber;
            }

            $transactionData['customer']['phone_numbers'] = [$phoneNumber];
        }
    }

    protected function appendBillingInformation(array &$transactionData, Payment $payment): void
    {
        $billingInformation = $payment->getBillingInformation();
        $customer = $payment->getInvoice()
            ->getSubscription()
            ->getCustomer();

        $transactionData['billing'] = [
            'name' => $billingInformation->getName() ?: $customer->getName(),
            'address' => $this->prepareAddressData($billingInformation),
        ];
    }

    /**
     * @return array<string, string>|array<string, null>
     */
    protected function prepareAddressData(BillingInformation $billingInformation): array
    {
        return [
            'country' => 'br',
            'street' => $billingInformation->getAddressLine1(),
            'street_number' => $billingInformation->getAddressNumber(),
            'state' => $billingInformation->getAddressState(),
            'city' => $billingInformation->getAddressCity(),
            'neighborhood' => $billingInformation->getAddressDistrict(),
            'zipcode' => preg_replace('/\D+/', '', $billingInformation->getAddressZipCode() ?? ''),
        ];
    }

    protected function appendItems(array &$transactionData, Payment $payment): void
    {
        $vendorPlan = $payment->getInvoice()->getSubscription()->getVendorPlan();
        $decimal = new Decimal($payment->getInvoice()->getTotalAmount());

        $transactionData['amount'] = $decimal->mul(100)->toFixed(0);
        $transactionData['items'] = [
            [
                'id' => $vendorPlan->getId()->toString(),
                'title' => $vendorPlan->getName(),
                'unit_price' => $decimal->mul(100)->toFixed(0),
                'quantity' => 1,
                'tangible' => false,
            ],
        ];
    }

    protected function appendSplitRules(array &$transactionData, Payment $payment): void
    {
        $vendorPlan = $payment->getInvoice()
            ->getSubscription()
            ->getVendorPlan();

        $vendorPagarmeId = $this->vendorSettingManager->getValue($vendorPlan->getVendor()->getId(), 'pagarme_id');

        $transactionData['split_rules'] = [
            [
                'recipient_id' => $this->pagarmeRecebedorId,
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

    protected function appendPostbackInformation(array &$transactionData, Payment $payment): void
    {
        $postbackUrl = $this->pagarmePostbackUrl;
        $postbackUrl .= '?reference='.$payment->getInvoice()->getSubscription()->getId()->toString();

        $transactionData['postback_url'] = $postbackUrl;
    }

    protected function validate(Payment $payment): void
    {
        if (null === $payment->getBillingInformation()) {
            throw new BillingInformationNotFoundException();
        }

        $billingInformation = $payment->getBillingInformation();

        if (null === $billingInformation->getDocumentNumber()) {
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
            $plan = $this->pagarmeClient->plans()
                ->create(
                    [
                        'amount' => $vendorPlan->getPrice()->mul(100)->toFixed(0),
                        'days' => $days,
                        'name' => $vendorPlan->getName(),
                    ]
                );
        } catch (PagarMeException $e) {
            $this->handlePagarmeVendorPlanException($vendorPlan, $e);
        }

        return $plan->id;
    }

    /**
     * @return mixed[]
     */
    abstract protected function prepareTransactionData(Payment $payment): array;

    private function refreshResponse(
        \stdClass $response,
        string $type,
        int $timeout = self::RESPONSE_TIMEOUT
    ): \stdClass {
        $start = time();

        while ('processing' === $response->status) {
            if (time() - $start >= $timeout) {
                break;
            }

            if ('subscription' === $type) {
                $response = $this->pagarmeClient->subscriptions()->get([
                    'id' => $response->id,
                ]);
            } else {
                $response = $this->pagarmeClient->transactions()->get([
                    'id' => $response->id,
                ]);
            }
        }

        return $response;
    }

    private function handlePagarmeVendorPlanException(VendorPlan $vendorPlan, PagarMeException $pagarMeException): void
    {
        $propertyName = $pagarMeException->getParameterName();

        throw new PagarmeInvalidInputException($vendorPlan, $propertyName, $pagarMeException->getMessage());
    }

    private function handlePagarmeSusbcriptionException(Payment $payment, \Exception $exception): void
    {
        $subscription = $payment->getInvoice()->getSubscription();

        $customerProperties = [
            'email' => 'email',
            'name' => 'name',
            'document_number' => 'documents',
            'customer[phone][ddd]' => 'phoneAreaCode',
            'customer[phone][number]' => 'phoneNumber',
        ];

        //message: "ERROR TYPE: validation_error. PARAMETER: billing. MESSAGE: \"zipcode\" must be 8 digits long for Brazilian addresses"
        //propertyPath: "billing"

        if (isset($customerProperties[$exception->getParameterName()])) {
            $propertyName = $customerProperties[$exception->getParameterName()] ?? $exception->getParameterName();

            throw new PagarmeInvalidInputException(
                $subscription->getCustomer(), $propertyName, $exception->getMessage()
            );
        }

        if ('split_rules[1][recipient_id]' === $exception->getParameterName()) {
            throw new MissingVendorBankAccountException($exception->getMessage());
        }

        $vendorProperties = [''];

        if (isset($vendorProperties[$exception->getParameterName()])) {
            $propertyName = $vendorProperties[$exception->getParameterName()] ?? $exception->getParameterName();

            throw new PagarmeInvalidInputException(
                $subscription->getVendorPlan()->getVendor(),
                $propertyName,
                $exception->getMessage()
            );
        }

        $paymentProperties = [
            'card_hash' => 'details',
        ];

        if (isset($paymentProperties[$exception->getParameterName()])) {
            $propertyName = $paymentProperties[$exception->getParameterName()] ?? $exception->getParameterName();

            throw new PagarmeInvalidInputException($payment, $propertyName, $exception->getMessage());
        }

        throw new PagarmeInvalidInputException(
            new \stdClass(), $exception->getParameterName(), $exception->getMessage()
        );
    }

    private function handlePagarmeTransactionException(\Throwable $throwable)
    {
        throw new PagarmeInvalidInputException(
            new \stdClass(), $throwable->getParameterName(), $throwable->getMessage()
        );
    }
}
