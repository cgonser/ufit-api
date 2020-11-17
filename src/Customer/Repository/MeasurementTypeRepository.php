<?php

namespace App\Customer\Repository;

use App\Customer\Entity\MeasurementType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MeasurementTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeasurementType::class);
    }

    public function save(MeasurementType $measurementType)
    {
        $this->getEntityManager()->persist($measurementType);
        $this->getEntityManager()->flush();
    }

    public function delete(MeasurementType $measurementType)
    {
        $this->getEntityManager()->remove($measurementType);
        $this->getEntityManager()->flush();
    }
}
