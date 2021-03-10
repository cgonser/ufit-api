<?php

namespace App\Payment\ResponseMapper;

use App\Core\ResponseMapper\PaymentMethodResponseMapper;
use App\Payment\Dto\PaymentDto;
use App\Payment\Entity\Payment;
use App\Vendor\ResponseMapper\VendorResponseMapper;

class PaymentResponseMapper
{
    private PaymentMethodResponseMapper $paymentMethodResponseMapper;

    public function __construct(PaymentMethodResponseMapper $paymentMethodResponseMapper)
    {
        $this->paymentMethodResponseMapper = $paymentMethodResponseMapper;
    }

    public function map(Payment $payment, bool $mapPaymentMethod = true): PaymentDto
    {
        $paymentDto = new PaymentDto();
        $paymentDto->id = $payment->getId()->toString();
        $paymentDto->invoiceId = $payment->getInvoiceId()->toString();
        $paymentDto->paymentMethodId = $payment->getPaymentMethodId()->toString();
        $paymentDto->amount = $payment->getAmount();
        $paymentDto->status = $payment->getStatus();
        $paymentDto->dueDate = $payment->getDueDate()->format('Y-m-d');
        $paymentDto->createdAt = $payment->getCreatedAt()->format(\DateTimeInterface::ISO8601);
        $paymentDto->updatedAt = $payment->getUpdatedAt()->format(\DateTimeInterface::ISO8601);

        if (null !== $payment->getPaidAt()) {
            $paymentDto->paidAt = $payment->getPaidAt()->format(\DateTimeInterface::ISO8601);
        }

        if ($mapPaymentMethod) {
            $paymentDto->paymentMethod = $this->paymentMethodResponseMapper->map($payment->getPaymentMethod());
        }

        return $paymentDto;
    }

    public function mapMultiple(array $payments, bool $mapPaymentMethod = true): array
    {
        $paymentDtos = [];

        foreach ($payments as $payment) {
            $paymentDtos[] = $this->map($payment, $mapPaymentMethod);
        }

        return $paymentDtos;
    }
}
