<?php

namespace App\Customer\Repository;

use App\Core\Repository\BaseRepository;
use App\Customer\Entity\CustomerPasswordResetToken;
use Doctrine\Persistence\ManagerRegistry;

class CustomerPasswordResetTokenRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerPasswordResetToken::class);
    }
}
