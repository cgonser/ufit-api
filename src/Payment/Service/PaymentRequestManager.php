<?php

declare(strict_types=1);

namespace App\Payment\Service;

use App\Customer\Provider\BillingInformationProvider;
use App\Customer\Service\BillingInformationRequestManager;
use App\Payment\Entity\Payment;
use App\Payment\Provider\InvoiceProvider;
use App\Payment\Provider\PaymentMethodProvider;
use App\Payment\Request\PaymentRequest;
use Ramsey\Uuid\Uuid;

class PaymentRequestManager
{
    private PaymentManager $paymentManager;
    private InvoiceProvider $invoiceProvider;
    private PaymentMethodProvider $paymentMethodProvider;
    private BillingInformationProvider $billingInformationProvider;
    private BillingInformationRequestManager $billingInformationRequestManager;

    public function __construct(
        PaymentManager $paymentManager,
        InvoiceProvider $invoiceProvider,
        PaymentMethodProvider $paymentMethodProvider,
        BillingInformationProvider $billingInformationProvider,
        BillingInformationRequestManager $billingInformationRequestManager
    ) {
        $this->paymentManager = $paymentManager;
        $this->paymentMethodProvider = $paymentMethodProvider;
        $this->invoiceProvider = $invoiceProvider;
        $this->billingInformationProvider = $billingInformationProvider;
        $this->billingInformationRequestManager = $billingInformationRequestManager;
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

    private function mapFromRequest(Payment $payment, PaymentRequest $paymentRequest): void
    {
        if ($paymentRequest->has('invoiceId')) {
            $invoice = $this->invoiceProvider->get(Uuid::fromString($paymentRequest->invoiceId));

            $payment->setInvoice($invoice);
            $payment->setAmount($invoice->getTotalAmount());
            $payment->setDueDate($invoice->getDueDate());
        }

        if ($paymentRequest->has('paymentMethodId')) {
            $payment->setPaymentMethod(
                $this->paymentMethodProvider->get(Uuid::fromString($paymentRequest->paymentMethodId))
            );
        }

        if ($paymentRequest->has('details')) {
            $payment->setDetails($paymentRequest->details);
        }

        if ($paymentRequest->has('billingInformationId')) {
            $payment->setBillingInformationId(Uuid::fromString($paymentRequest->billingInformationId));
        }

        if ($paymentRequest->has('billingInformation') && null !== $payment->getInvoice()->getSubscription()) {
            $paymentRequest->billingInformation->customerId = $payment->getInvoice()
                ->getSubscription()
                ->getCustomerId();

            if (null === $payment->getBillingInformationId()) {
                $billingInformation = $this->billingInformationRequestManager->createFromRequest(
                    $paymentRequest->billingInformation
                );
            } else {
                $billingInformation = $this->billingInformationProvider->get(
                    Uuid::fromString($paymentRequest->billingInformationId)
                );

                $this->billingInformationRequestManager->updateFromRequest(
                    $billingInformation,
                    $paymentRequest->billingInformation
                );
            }

            $payment->setBillingInformation($billingInformation);
        }
    }
}
