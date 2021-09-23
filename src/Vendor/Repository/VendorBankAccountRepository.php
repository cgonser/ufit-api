<?php

declare(strict_types=1);

namespace App\Vendor\Repository;

use App\Core\Repository\BaseRepository;
use App\Vendor\Entity\VendorBankAccount;
use Doctrine\Persistence\ManagerRegistry;

class VendorBankAccountRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, VendorBankAccount::class);
    }
}
