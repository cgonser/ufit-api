<?php

namespace App\Payment\Repository;

use App\Payment\Entity\PaymentMethod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PaymentMethodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentMethod::class);
    }

    public function save(PaymentMethod $paymentMethod)
    {
        $this->getEntityManager()->persist($paymentMethod);
        $this->getEntityManager()->flush();
    }

    public function delete(PaymentMethod $paymentMethod)
    {
        $this->getEntityManager()->remove($paymentMethod);
        $this->getEntityManager()->flush();
    }
}
