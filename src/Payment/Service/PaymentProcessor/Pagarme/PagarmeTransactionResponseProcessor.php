<?php

declare(strict_types=1);

namespace App\Payment\Service\PaymentProcessor\Pagarme;

use App\Payment\Entity\Payment;
use App\Payment\Exception\PaymentNotFoundException;
use App\Payment\Provider\PaymentMethodProvider;
use App\Payment\Provider\PaymentProvider;
use App\Payment\Service\PaymentManager;
use App\Subscription\Service\SubscriptionManager;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\String\UnicodeString;

class PagarmeTransactionResponseProcessor
{
    public function __construct(
        private PaymentProvider $paymentProvider,
        private PaymentManager $paymentManager,
        private PaymentMethodProvider $paymentMethodProvider,
        private SubscriptionManager $subscriptionManager
    ) {
    }

    public function process(
        \stdClass $response,
        ?UuidInterface $paymentId = null,
        ?UuidInterface $subscriptionId = null
    ): void {
        $payment = $this->getOrCreatePayment(
            (string)$response->tid,
            $paymentId,
            $subscriptionId,
            $response->payment_method
        );

        $methodName = 'process'.ucfirst((new UnicodeString($response->status))->camel()->toString());

        $gatewayResponse = $payment->getGatewayResponse();
        if (null === $payment->getGatewayResponse()) {
            $gatewayResponse = [];
        }

        $gatewayResponse[date('Ymd_his')] = (array)$response;
        $payment->setGatewayResponse($gatewayResponse);

        if (method_exists($this, $methodName)) {
            $this->{$methodName}($payment, $response);
        }

        $this->paymentManager->update($payment);
    }

    private function getOrCreatePayment(
        string $externalReference,
        ?UuidInterface $paymentId,
        ?UuidInterface $subscriptionId,
        string $paymentMethodName
    ): Payment {
        try {
            if (null !== $paymentId) {
                /** @var Payment $payment */
                $payment = $this->paymentProvider->get($paymentId);
                $payment->setExternalReference($externalReference);

                return $payment;
            }

            return $this->paymentProvider->getByExternalReference($externalReference);
        } catch (PaymentNotFoundException $paymentNotFoundException) {
            if (null !== $subscriptionId) {
                // todo: generate invoice + payment
                $invoice = $this->subscriptionManager->getOrCreateUnpaidInvoice($subscriptionId);

                $paymentMethod = $this->paymentMethodProvider->getBy([
                    'name' => [
                            'credit_card' => 'credit-card',
                            'boleto' => 'boleto',
                        ][$paymentMethodName] ?? $paymentMethodName,
                ]);

                $payment = $this->paymentManager->createFromInvoice($invoice, $paymentMethod);
                $payment->setExternalReference($externalReference);

                return $payment;
            }

            throw $paymentNotFoundException;
        }
    }

    private function processPaid(Payment $payment, \stdClass $response): void
    {
        $this->paymentManager->markAsPaid($payment, new \DateTime($response->date_updated));
    }

    private function processProcessing(Payment $payment, \stdClass $response): void
    {
        $payment->setStatus(Payment::STATUS_PENDING);
    }

    private function processAuthorized(Payment $payment, \stdClass $response): void
    {
//        $payment->setStatus(Payment::STATUS_PENDING);
    }

    private function processRefunded(Payment $payment, \stdClass $response): void
    {
//        $payment->setStatus(Payment::STATUS_PENDING);
    }

    private function processWaitingPayment(Payment $payment, \stdClass $response): void
    {
        $payment->setStatus(Payment::STATUS_PENDING);
        $payment->setDetails(
            array_merge(
                $payment->getDetails() ?? [],
                [
                    'boleto_url' => $response->boleto_url,
                    'boleto_barcode' => $response->boleto_barcode,
                    'boleto_expiration_date' => $response->boleto_expiration_date,
                ]
            )
        );
        $payment->setDueDate(new \DateTime($response->boleto_expiration_date));
    }

    private function processPendingRefund(Payment $payment, \stdClass $response): void
    {
//        $payment->setStatus(Payment::STATUS_PENDING);
    }

    private function processRefused(Payment $payment, \stdClass $response): void
    {
        $payment->setStatus(Payment::STATUS_REJECTED);
    }

    private function processChargedback(Payment $payment, \stdClass $response): void
    {
//        $payment->setStatus(Payment::STATUS_PENDING);
    }

    private function processAnalyzing(Payment $payment, \stdClass $response): void
    {
        $payment->setStatus(Payment::STATUS_PENDING);
    }

    private function processPendingReview(Payment $payment, \stdClass $response): void
    {
        $payment->setStatus(Payment::STATUS_PENDING);
    }
}
