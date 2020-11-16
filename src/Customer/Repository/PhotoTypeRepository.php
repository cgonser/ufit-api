<?php

namespace App\Customer\Repository;

use App\Customer\Entity\PhotoType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PhotoTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhotoType::class);
    }

    public function save(PhotoType $photoType)
    {
        $this->getEntityManager()->persist($photoType);
        $this->getEntityManager()->flush();
    }
}
