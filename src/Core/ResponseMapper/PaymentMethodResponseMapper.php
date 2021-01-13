<?php

namespace App\Core\ResponseMapper;

use App\Core\Dto\PaymentMethodDto;
use App\Core\Entity\PaymentMethod;

class PaymentMethodResponseMapper
{
    public function map(PaymentMethod $paymentMethod): PaymentMethodDto
    {
        $paymentMethodDto = new PaymentMethodDto();
        $paymentMethodDto->id = $paymentMethod->getId()->toString();
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