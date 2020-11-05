<?php

namespace App\Customer\Repository;

use App\Customer\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function save(Customer $customer)
    {
        $this->getEntityManager()->persist($customer);
        $this->getEntityManager()->flush();
    }
}
