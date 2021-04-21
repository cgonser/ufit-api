<?php

namespace App\Payment\Service;

use App\Payment\Entity\PaymentMethod;
use App\Payment\Repository\PaymentMethodRepository;

class PaymentMethodManager
{
    private PaymentMethodRepository $paymentMethodRepository;

    public function __construct(
        PaymentMethodRepository $paymentMethodRepository
    ) {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function create(PaymentMethod $paymentMethod): void
    {
        $this->paymentMethodRepository->save($paymentMethod);
    }

    public function update(PaymentMethod $paymentMethod)
    {
        $this->paymentMethodRepository->save($paymentMethod);
    }

    public function delete(PaymentMethod $paymentMethod)
    {
        $this->paymentMethodRepository->delete($paymentMethod);
    }
}
