<?php

declare(strict_types=1);

namespace App\Payment\ResponseMapper;

use App\Payment\Dto\PaymentMethodDto;
use App\Payment\Entity\PaymentMethod;

class PaymentMethodResponseMapper
{
    public function map(PaymentMethod $paymentMethod): PaymentMethodDto
    {
        $paymentMethodDto = new PaymentMethodDto();
        $paymentMethodDto->id = $paymentMethod->getId()
            ->toString();
        $paymentMethodDto->name = $paymentMethod->getName();
        $paymentMethodDto->countriesEnabled = $paymentMethod->getCountriesEnabled();
        $paymentMethodDto->countriesDisabled = $paymentMethod->getCountriesDisabled();
        $paymentMethodDto->isActive = $paymentMethod->isActive();

        return $paymentMethodDto;
    }

    public function mapMultiple(array $paymentMethods): array
    {
        $paymentMethodDtos = [];

        foreach ($paymentMethods as $paymentMethod) {
            $paymentMethodDtos[] = $this->map($paymentMethod);
        }

        return $paymentMethodDtos;
    }
}
