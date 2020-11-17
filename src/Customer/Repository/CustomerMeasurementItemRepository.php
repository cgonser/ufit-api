<?php

namespace App\Customer\Repository;

use App\Customer\Entity\CustomerMeasurementItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CustomerMeasurementItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerMeasurementItem::class);
    }

    public function save(CustomerMeasurementItem $customerMeasurementItem)
    {
        $this->getEntityManager()->persist($customerMeasurementItem);
        $this->getEntityManager()->flush();
    }
}
