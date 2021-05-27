<?php

namespace App\Payment\Service\PaymentProcessor\Pagarme;

use App\Payment\Entity\Payment;
use App\Payment\Message\InvoicePaidEvent;
use App\Payment\Provider\PaymentProvider;
use App\Payment\Service\PaymentManager;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\String\UnicodeString;

class PagarmeTransactionResponseProcessor
{
    private PaymentProvider $paymentProvider;

    private PaymentManager $paymentManager;

    private MessageBusInterface $messageBus;

    public function __construct(
        PaymentProvider $paymentProvider,
        PaymentManager $paymentManager,
        MessageBusInterface $messageBus
    ) {
        $this->paymentProvider = $paymentProvider;
        $this->paymentManager = $paymentManager;
        $this->messageBus = $messageBus;
    }

    public function process(\stdClass $response, ?UuidInterface $paymentId = null)
    {
        if (null !== $paymentId) {
            /** @var Payment $payment */
            $payment = $this->paymentProvider->get($paymentId);
            $payment->setExternalReference((string) $response->id);
        } else {
            // todo: fetch by external reference
            $payment = $this->paymentProvider->getByExternalReference((string) $response->id);
        }

        $status = new UnicodeString($response->status);
        $methodName = 'process'.ucfirst($status->camel());

        $gatewayResponse = $payment->getGatewayResponse();
        if (null === $payment->getGatewayResponse()) {
            $gatewayResponse = [];
        }

        $gatewayResponse[date('Ymd_his')] = (array) $response;
        $payment->setGatewayResponse($gatewayResponse);

        if (method_exists($this, $methodName)) {
            $this->$methodName($payment, $response);
        }

        $this->paymentManager->update($payment);
    }

    private function processPaid(Payment $payment, \stdClass $response)
    {
        $payment->setStatus(Payment::STATUS_PAID);
        $payment->setPaidAt(new \DateTime($response->date_updated));

        $this->messageBus->dispatch(
            new InvoicePaidEvent($payment->getInvoiceId(), $payment->getPaidAt())
        );
    }

    private function processProcessing(Payment $payment, \stdClass $response)
    {
        $payment->setStatus(Payment::STATUS_PENDING);
    }

    private function processAuthorized(Payment $payment, \stdClass $response)
    {
//        $payment->setStatus(Payment::STATUS_PENDING);
    }

    private function processRefunded(Payment $payment, \stdClass $response)
    {
//        $payment->setStatus(Payment::STATUS_PENDING);
    }

    private function processWaitingPayment(Payment $payment, \stdClass $response)
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

    private function processPendingRefund(Payment $payment, \stdClass $response)
    {
//        $payment->setStatus(Payment::STATUS_PENDING);
    }

    private function processRefused(Payment $payment, \stdClass $response)
    {
        $payment->setStatus(Payment::STATUS_REJECTED);
    }

    private function processChargedback(Payment $payment, \stdClass $response)
    {
//        $payment->setStatus(Payment::STATUS_PENDING);
    }

    private function processAnalyzing(Payment $payment, \stdClass $response)
    {
        $payment->setStatus(Payment::STATUS_PENDING);
    }

    private function processPendingReview(Payment $payment, \stdClass $response)
    {
        $payment->setStatus(Payment::STATUS_PENDING);
    }
}
