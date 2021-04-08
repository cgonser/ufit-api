<?php

namespace App\Payment\Service;

use App\Core\Provider\PaymentMethodProvider;
use App\Payment\Entity\Payment;
use App\Payment\Provider\InvoiceProvider;
use App\Payment\Request\PaymentRequest;
use Ramsey\Uuid\Uuid;

class PaymentRequestManager
{
    private PaymentManager $paymentManager;

    private InvoiceProvider $invoiceProvider;

    private PaymentMethodProvider $paymentMethodProvider;

    public function __construct(
        PaymentManager $paymentManager,
        InvoiceProvider $invoiceProvider,
        PaymentMethodProvider $paymentMethodProvider
    ) {
        $this->paymentManager = $paymentManager;
        $this->paymentMethodProvider = $paymentMethodProvider;
        $this->invoiceProvider = $invoiceProvider;
    }

    public function createFromRequest(PaymentRequest $paymentRequest): Payment
    {
        $payment = new Payment();

        $this->mapFromRequest($payment, $paymentRequest);

        $this->paymentManager->create($payment);

        return $payment;
    }

    public function updateFromRequest(Payment $payment, PaymentRequest $paymentRequest)
    {
        $this->mapFromRequest($payment, $paymentRequest);

        $this->paymentManager->update($payment);
    }

    private function mapFromRequest(Payment $payment, PaymentRequest $paymentRequest)
    {
        if (null !== $paymentRequest->invoiceId) {
            $invoice = $this->invoiceProvider->get(Uuid::fromString($paymentRequest->invoiceId));

            $payment->setInvoice($invoice);
            $payment->setAmount($invoice->getTotalAmount());
            $payment->setDueDate($invoice->getDueDate());
        }

        if (null !== $paymentRequest->paymentMethodId) {
            $payment->setPaymentMethod(
                $this->paymentMethodProvider->get(Uuid::fromString($paymentRequest->paymentMethodId))
            );
        }

        if (null !== $paymentRequest->details) {
            $payment->setDetails($paymentRequest->details);
        }
    }
}
