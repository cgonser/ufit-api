<?php

namespace App\Vendor\Repository;

use App\Core\Repository\BaseRepository;
use App\Vendor\Entity\VendorSetting;
use Doctrine\Persistence\ManagerRegistry;

class VendorSettingRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VendorSetting::class);
    }
}
