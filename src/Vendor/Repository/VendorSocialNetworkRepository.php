<?php

declare(strict_types=1);

namespace App\Vendor\Repository;

use App\Core\Repository\BaseRepository;
use App\Vendor\Entity\VendorSocialNetwork;
use Doctrine\Persistence\ManagerRegistry;

class VendorSocialNetworkRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, VendorSocialNetwork::class);
    }
}
