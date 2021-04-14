<?php

namespace App\Vendor\Repository;

use App\Core\Repository\BaseRepository;
use App\Vendor\Entity\VendorPasswordResetToken;
use Doctrine\Persistence\ManagerRegistry;

class VendorPasswordResetTokenRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VendorPasswordResetToken::class);
    }
}
