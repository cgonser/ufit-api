<?php

namespace App\Customer\Repository;

use App\Customer\Entity\CustomerMeasureItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CustomerMeasureItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerMeasureItem::class);
    }

    public function save(CustomerMeasureItem $customerMeasureItem)
    {
        $this->getEntityManager()->persist($customerMeasureItem);
        $this->getEntityManager()->flush();
    }
}
