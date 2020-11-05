<?php

namespace App\Vendor\Repository;

use App\Vendor\Entity\Vendor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VendorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vendor::class);
    }

    public function save(Vendor $vendor)
    {
        $this->getEntityManager()->persist($vendor);
        $this->getEntityManager()->flush();
    }
}
