<?php

namespace App\Customer\Repository;

use App\Customer\Entity\CustomerMeasure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CustomerMeasureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerMeasure::class);
    }

    public function save(CustomerMeasure $customerMeasure)
    {
        $this->getEntityManager()->persist($customerMeasure);
        $this->getEntityManager()->flush();
    }
}
