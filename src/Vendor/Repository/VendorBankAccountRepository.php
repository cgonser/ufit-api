<?php

namespace App\Vendor\Repository;

use App\Core\Repository\BaseRepository;
use App\Vendor\Entity\VendorBankAccount;
use Doctrine\Persistence\ManagerRegistry;

class VendorBankAccountRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VendorBankAccount::class);
    }
}
