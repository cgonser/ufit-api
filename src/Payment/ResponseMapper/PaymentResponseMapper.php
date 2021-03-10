<?php

namespace App\Payment\ResponseMapper;

use App\Payment\Dto\PaymentDto;
use App\Payment\Entity\Payment;
use App\Vendor\ResponseMapper\VendorResponseMapper;

class PaymentResponseMapper
{
    private VendorResponseMapper $vendorResponseMapper;

    public function __construct(VendorResponseMapper $vendorResponseMapper)
    {
        $this->vendorResponseMapper = $vendorResponseMapper;
    }

    public function map(Payment $payment, bool $mapRelations = true): PaymentDto
    {
        $paymentDto = new PaymentDto();
        $paymentDto->id = $payment->getId()->toString();
        $paymentDto->vendorId = $payment->getVendor()->getId()->toString();
        $paymentDto->name = $payment->getName();
        $paymentDto->level = $payment->getLevel();
        $paymentDto->goals = $payment->getGoals();
        $paymentDto->isTemplate = $payment->isTemplate();
        $paymentDto->createdAt = $payment->getCreatedAt()->format(\DateTimeInterface::ISO8601);
        $paymentDto->updatedAt = $payment->getUpdatedAt()->format(\DateTimeInterface::ISO8601);

        if ($mapRelations) {
            $paymentDto->assets = $this->vendorResponseMapper->mapMultiple($payment->getAssets()->toArray());
        }

        return $paymentDto;
    }

    public function mapMultiple(array $payments, bool $mapAssets = false): array
    {
        $paymentDtos = [];

        foreach ($payments as $payment) {
            $paymentDtos[] = $this->map($payment, $mapAssets);
        }

        return $paymentDtos;
    }
}
