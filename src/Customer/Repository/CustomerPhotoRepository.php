<?php

namespace App\Customer\Repository;

use App\Customer\Entity\CustomerPhoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CustomerPhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerPhoto::class);
    }

    public function save(CustomerPhoto $customerPhoto)
    {
        $this->getEntityManager()->persist($customerPhoto);
        $this->getEntityManager()->flush();
    }

    public function delete(CustomerPhoto $customerPhoto)
    {
        $this->getEntityManager()->remove($customerPhoto);
        $this->getEntityManager()->flush();
    }
}
