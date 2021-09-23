<?php

declare(strict_types=1);

namespace App\Vendor\Repository;

use App\Core\Repository\BaseRepository;
use App\Vendor\Entity\VendorPlan;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VendorPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method VendorPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method VendorPlan[]    findAll()
 * @method VendorPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VendorPlanRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, VendorPlan::class);
    }
}
