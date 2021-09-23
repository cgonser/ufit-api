<?php

declare(strict_types=1);

namespace App\Payment\Service;

use App\Payment\Entity\PaymentMethod;
use App\Payment\Repository\PaymentMethodRepository;

class PaymentMethodManager
{
    public function __construct(private PaymentMethodRepository $paymentMethodRepository)
    {
    }

    public function create(PaymentMethod $paymentMethod): void
    {
        $this->paymentMethodRepository->save($paymentMethod);
    }

    public function update(PaymentMethod $paymentMethod): void
    {
        $this->paymentMethodRepository->save($paymentMethod);
    }

    public function delete(PaymentMethod $paymentMethod): void
    {
        $this->paymentMethodRepository->delete($paymentMethod);
    }
}
