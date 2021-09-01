<?php

declare(strict_types=1);

namespace App\Payment\Repository;

use App\Payment\Entity\PaymentMethod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PaymentMethodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, PaymentMethod::class);
    }

    public function save(PaymentMethod $paymentMethod): void
    {
        $this->getEntityManager()
            ->persist($paymentMethod);
        $this->getEntityManager()
            ->flush();
    }

    public function delete(PaymentMethod $paymentMethod): void
    {
        $this->getEntityManager()
            ->remove($paymentMethod);
        $this->getEntityManager()
            ->flush();
    }
}
