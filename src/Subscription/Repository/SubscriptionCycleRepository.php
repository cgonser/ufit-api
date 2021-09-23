<?php

declare(strict_types=1);

namespace App\Subscription\Repository;

use App\Core\Repository\BaseRepository;
use App\Subscription\Entity\SubscriptionCycle;
use Doctrine\Persistence\ManagerRegistry;

class SubscriptionCycleRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, SubscriptionCycle::class);
    }
}
