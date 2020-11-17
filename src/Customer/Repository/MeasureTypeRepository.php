<?php

namespace App\Customer\Repository;

use App\Customer\Entity\MeasureType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MeasureTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeasureType::class);
    }

    public function save(MeasureType $measureType)
    {
        $this->getEntityManager()->persist($measureType);
        $this->getEntityManager()->flush();
    }

    public function delete(MeasureType $measureType)
    {
        $this->getEntityManager()->remove($measureType);
        $this->getEntityManager()->flush();
    }
}
