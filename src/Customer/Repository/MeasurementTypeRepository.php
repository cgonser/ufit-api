<?php

namespace App\Customer\Repository;

use App\Core\Repository\BaseRepository;
use App\Customer\Entity\MeasurementType;
use Doctrine\Persistence\ManagerRegistry;

class MeasurementTypeRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeasurementType::class);
    }
}
