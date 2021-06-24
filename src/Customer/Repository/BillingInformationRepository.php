<?php

namespace App\Customer\Repository;

use App\Core\Repository\BaseRepository;
use App\Customer\Entity\BillingInformation;
use Doctrine\Persistence\ManagerRegistry;

class BillingInformationRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BillingInformation::class);
    }
}
