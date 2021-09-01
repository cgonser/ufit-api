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

        $unicodeString = new UnicodeString($response->status);
        $methodName = 'process'.ucfirst($unicodeString->camel()->toString());

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
}
