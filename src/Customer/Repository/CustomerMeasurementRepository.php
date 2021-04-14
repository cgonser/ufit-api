<?php

namespace App\Customer\Repository;

use App\Core\Repository\BaseRepository;
use App\Customer\Entity\CustomerMeasurement;
use Doctrine\Persistence\ManagerRegistry;

class CustomerMeasurementRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerMeasurement::class);
    }
}
