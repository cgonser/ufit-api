<?php

declare(strict_types=1);

namespace App\Vendor\Repository;

use App\Core\Repository\BaseRepository;
use App\Vendor\Entity\VendorInstagramProfile;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VendorInstagramProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method VendorInstagramProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method VendorInstagramProfile[]    findAll()
 * @method VendorInstagramProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VendorInstagramProfileRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, VendorInstagramProfile::class);
    }
}
