<?php

namespace App\Payment\ResponseMapper;

use App\Localization\ResponseMapper\CurrencyResponseMapper;
use App\Payment\Dto\PaymentDto;
use App\Payment\Entity\Payment;
use App\Vendor\ResponseMapper\VendorPlanResponseMapper;

class PaymentResponseMapper
{
    private PaymentMethodResponseMapper $paymentMethodResponseMapper;
    private CurrencyResponseMapper $currencyResponseMapper;
    private VendorPlanResponseMapper $vendorPlanResponseMapper;

    public function __construct(
        PaymentMethodResponseMapper $paymentMethodResponseMapper,
        CurrencyResponseMapper $currencyResponseMapper,
        VendorPlanResponseMapper $vendorPlanResponseMapper
    ) {
        $this->paymentMethodResponseMapper = $paymentMethodResponseMapper;
        $this->currencyResponseMapper = $currencyResponseMapper;
        $this->vendorPlanResponseMapper = $vendorPlanResponseMapper;
    }

    public function map(
        Payment $payment,
        bool $mapPaymentMethod = true,
        bool $mapCurrency = true,
        bool $mapVendorPlan = true
    ): PaymentDto {
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

        if ($mapVendorPlan) {
            $paymentDto->vendorPlan = $this->vendorPlanResponseMapper->map($payment->getInvoice()->getSubscription()->getVendorPlan());
        }

        return $paymentDto;
    }

    public function mapMultiple(
        array $payments,
        bool $mapPaymentMethod = true,
        bool $mapCurrency = true,
        bool $mapVendorPlan = true
    ): array {
        $paymentDtos = [];

        foreach ($payments as $payment) {
            $paymentDtos[] = $this->map($payment, $mapPaymentMethod, $mapCurrency, $mapVendorPlan);
        }

        return $paymentDtos;
    }
}
