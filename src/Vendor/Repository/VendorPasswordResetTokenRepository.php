<?php

declare(strict_types=1);

namespace App\Vendor\Repository;

use App\Core\Repository\BaseRepository;
use App\Vendor\Entity\VendorPasswordResetToken;
use Doctrine\Persistence\ManagerRegistry;

class VendorPasswordResetTokenRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, VendorPasswordResetToken::class);
    }
}
