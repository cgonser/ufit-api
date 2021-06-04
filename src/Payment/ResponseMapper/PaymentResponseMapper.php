<?php

namespace App\Payment\ResponseMapper;

use App\Localization\ResponseMapper\CurrencyResponseMapper;
use App\Payment\Dto\PaymentDto;
use App\Payment\Entity\Payment;

class PaymentResponseMapper
{
    private PaymentMethodResponseMapper $paymentMethodResponseMapper;
    private CurrencyResponseMapper $currencyResponseMapper;

    public function __construct(
        PaymentMethodResponseMapper $paymentMethodResponseMapper,
        CurrencyResponseMapper $currencyResponseMapper
    ) {
        $this->paymentMethodResponseMapper = $paymentMethodResponseMapper;
        $this->currencyResponseMapper = $currencyResponseMapper;
    }

    public function map(Payment $payment, bool $mapPaymentMethod = true, bool $mapCurrency = true): PaymentDto
    {
        $paymentDto = new PaymentDto();
        $paymentDto->id = $payment->getId()->toString();
        $paymentDto->invoiceId = $payment->getInvoiceId()->toString();
        $paymentDto->currencyId = $payment->getInvoice()->getCurrencyId()->toString();
        $paymentDto->paymentMethodId = $payment->getPaymentMethodId()->toString();
        $paymentDto->amount = $payment->getAmount();
        $paymentDto->status = $payment->getStatus();
        $paymentDto->details = $payment->getDetails();
        $paymentDto->dueDate = $payment->getDueDate()->format('Y-m-d');
        $paymentDto->createdAt = $payment->getCreatedAt()->format(\DateTimeInterface::ATOM);
        $paymentDto->updatedAt = $payment->getUpdatedAt()->format(\DateTimeInterface::ATOM);

        if (null !== $payment->getPaidAt()) {
            $paymentDto->paidAt = $payment->getPaidAt()->format(\DateTimeInterface::ATOM);
        }

        if ($mapPaymentMethod) {
            $paymentDto->paymentMethod = $this->paymentMethodResponseMapper->map($payment->getPaymentMethod());
        }

        if ($mapCurrency) {
            $paymentDto->currency = $this->currencyResponseMapper->map($payment->getInvoice()->getCurrency());
        }

        return $paymentDto;
    }

    public function mapMultiple(array $payments, bool $mapPaymentMethod = true, bool $mapCurrency = true): array
    {
        $paymentDtos = [];

        foreach ($payments as $payment) {
            $paymentDtos[] = $this->map($payment, $mapPaymentMethod);
        }

        return $paymentDtos;
    }
}
