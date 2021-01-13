<?php

namespace App\Core\Service;

use App\Core\Entity\PaymentMethod;
use App\Core\Provider\PaymentMethodProvider;
use App\Core\Repository\PaymentMethodRepository;
use App\Core\Request\PaymentMethodRequest;

class PaymentMethodService
{
    private PaymentMethodRepository $paymentMethodRepository;

    private PaymentMethodProvider $paymentMethodProvider;

    public function __construct(
        PaymentMethodRepository $paymentMethodRepository,
        PaymentMethodProvider $paymentMethodProvider
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentMethodProvider = $paymentMethodProvider;
    }

    public function create(PaymentMethodRequest $paymentMethodRequest): PaymentMethod
    {
        $paymentMethod = new PaymentMethod();

        $this->mapFromRequest($paymentMethod, $paymentMethodRequest);

        $this->paymentMethodRepository->save($paymentMethod);

        return $paymentMethod;
    }

    public function update(PaymentMethod $paymentMethod, PaymentMethodRequest $paymentMethodRequest)
    {
        $this->mapFromRequest($paymentMethod, $paymentMethodRequest);

        $this->paymentMethodRepository->save($paymentMethod);
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

    public function delete(PaymentMethod $paymentMethod)
    {
        $this->paymentMethodRepository->delete($paymentMethod);
    }
}
