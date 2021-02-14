<?php

namespace App\Subscription\Repository;

use App\Core\Repository\BaseRepository;
use App\Subscription\Entity\SubscriptionCycle;
use Doctrine\Persistence\ManagerRegistry;

class SubscriptionCycleRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscriptionCycle::class);
    }
}
