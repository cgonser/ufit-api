<?php

namespace App\Customer\Repository;

use App\Customer\Entity\CustomerMeasurement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CustomerMeasurementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerMeasurement::class);
    }

    public function save(CustomerMeasurement $customerMeasurement)
    {
        $this->getEntityManager()->persist($customerMeasurement);
        $this->getEntityManager()->flush();
    }

    public function delete(CustomerMeasurement $customerMeasurement)
    {
        $this->getEntityManager()->remove($customerMeasurement);
        $this->getEntityManager()->flush();
    }
}
