<?php

declare(strict_types=1);

namespace App\Customer\Repository;

use App\Core\Repository\BaseRepository;
use App\Customer\Entity\CustomerSocialNetwork;
use Doctrine\Persistence\ManagerRegistry;

class CustomerSocialNetworkRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerSocialNetwork::class);
    }
}
