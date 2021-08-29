<?php

declare(strict_types=1);

namespace App\Payment\Repository;

use App\Core\Repository\BaseRepository;
use App\Payment\Entity\Invoice;
use Doctrine\Persistence\ManagerRegistry;

class InvoiceRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }
}
