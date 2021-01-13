<?php

namespace App\Core\Provider;

use App\Core\Entity\PaymentMethod;
use App\Core\Exception\PaymentMethodNotFoundException;
use App\Core\Repository\PaymentMethodRepository;
use Ramsey\Uuid\UuidInterface;

class PaymentMethodProvider
{
    private PaymentMethodRepository $paymentMethodRepository;

    public function __construct(PaymentMethodRepository $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function get(UuidInterface $currencyId): PaymentMethod
    {
        /** @var PaymentMethod|null $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->find($currencyId);

        if (!$paymentMethod) {
            throw new PaymentMethodNotFoundException();
        }

        return $paymentMethod;
    }

    public function findAll(): array
    {
        return $this->paymentMethodRepository->findAll();
    }
}