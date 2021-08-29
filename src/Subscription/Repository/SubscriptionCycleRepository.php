<?php

declare(strict_types=1);

namespace App\Subscription\Repository;

use App\Core\Repository\BaseRepository;
use App\Subscription\Entity\SubscriptionCycle;
use Doctrine\Persistence\ManagerRegistry as ManagerRegistryAlias;

class SubscriptionCycleRepository extends BaseRepository
{
    public function __construct(ManagerRegistryAlias $registry)
    {
        parent::__construct($registry, SubscriptionCycle::class);
    }
}
