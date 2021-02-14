<?php

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VendorInstagramProfile::class);
    }
}
