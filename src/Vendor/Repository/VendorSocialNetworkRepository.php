<?php

namespace App\Vendor\Repository;

use App\Core\Repository\BaseRepository;
use App\Vendor\Entity\VendorSocialNetwork;
use Doctrine\Persistence\ManagerRegistry;

class VendorSocialNetworkRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VendorSocialNetwork::class);
    }
}
