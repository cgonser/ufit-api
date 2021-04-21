<?php

namespace App\Payment\Service;

use App\Payment\Entity\PaymentMethod;
use App\Payment\Request\PaymentMethodRequest;

class PaymentMethodRequestManager
{
    private PaymentMethodManager $paymentMethodManager;

    public function __construct(
        PaymentMethodManager $paymentMethodManager
    ) {
        $this->paymentMethodManager = $paymentMethodManager;
    }

    public function createFromRequest(PaymentMethodRequest $paymentMethodRequest): PaymentMethod
    {
        $paymentMethod = new PaymentMethod();

        $this->mapFromRequest($paymentMethod, $paymentMethodRequest);

        $this->paymentMethodManager->create($paymentMethod);

        return $paymentMethod;
    }

    public function updateFromRequest(PaymentMethod $paymentMethod, PaymentMethodRequest $paymentMethodRequest)
    {
        $this->mapFromRequest($paymentMethod, $paymentMethodRequest);

        $this->paymentMethodManager->update($paymentMethod);
    }

    public function mapFromRequest(PaymentMethod $paymentMethod, PaymentMethodRequest $paymentMethodRequest)
    {
        if (null !== $paymentMethodRequest->name) {
            $paymentMethod->setName($paymentMethodRequest->name);
        }

        if (null !== $paymentMethodRequest->countriesEnabled) {
            $paymentMethod->setCountriesEnabled($paymentMethodRequest->countriesEnabled);
        }

        if (null !== $paymentMethodRequest->countriesDisabled) {
            $paymentMethod->setCountriesDisabled($paymentMethodRequest->countriesDisabled);
        }

        if (null !== $paymentMethodRequest->isActive) {
            $paymentMethod->setIsActive($paymentMethodRequest->isActive);
        }
    }
}
