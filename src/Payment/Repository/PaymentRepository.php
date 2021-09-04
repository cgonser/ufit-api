<?php

declare(strict_types=1);

namespace App\Payment\Repository;

use App\Core\Repository\BaseRepository;
use App\Payment\Entity\Payment;
use Doctrine\Persistence\ManagerRegistry;

class PaymentRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Payment::class);
    }
}
