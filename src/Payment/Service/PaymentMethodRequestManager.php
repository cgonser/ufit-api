<?php

declare(strict_types=1);

namespace App\Payment\Service;

use App\Payment\Entity\PaymentMethod;
use App\Payment\Request\PaymentMethodRequest;

class PaymentMethodRequestManager
{
    public function __construct(private PaymentMethodManager $paymentMethodManager)
    {
    }

    public function createFromRequest(PaymentMethodRequest $paymentMethodRequest): PaymentMethod
    {
        $paymentMethod = new PaymentMethod();

        $this->mapFromRequest($paymentMethod, $paymentMethodRequest);

        $this->paymentMethodManager->create($paymentMethod);

        return $paymentMethod;
    }

    public function updateFromRequest(PaymentMethod $paymentMethod, PaymentMethodRequest $paymentMethodRequest): void
    {
        $this->mapFromRequest($paymentMethod, $paymentMethodRequest);

        $this->paymentMethodManager->update($paymentMethod);
    }

    public function mapFromRequest(PaymentMethod $paymentMethod, PaymentMethodRequest $paymentMethodRequest): void
    {
        if ($paymentMethodRequest->has('name')) {
            $paymentMethod->setName($paymentMethodRequest->name);
        }

        if ($paymentMethodRequest->has('countriesEnabled')) {
            $paymentMethod->setCountriesEnabled($paymentMethodRequest->countriesEnabled);
        }

        if ($paymentMethodRequest->has('countriesDisabled')) {
            $paymentMethod->setCountriesDisabled($paymentMethodRequest->countriesDisabled);
        }

        if ($paymentMethodRequest->has('isActive')) {
            $paymentMethod->setIsActive($paymentMethodRequest->isActive);
        }
    }
}
