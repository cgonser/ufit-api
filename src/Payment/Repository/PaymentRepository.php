<?php

namespace App\Payment\Repository;

use App\Core\Repository\BaseRepository;
use App\Payment\Entity\Payment;
use Doctrine\Persistence\ManagerRegistry;

class PaymentRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }
}
