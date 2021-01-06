<?php

namespace App\Vendor\Repository;

use App\Vendor\Entity\VendorInstagramProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VendorInstagramProfile|null find($id, $lockMode = null, $lockVersion = null)
 * @method VendorInstagramProfile|null findOneBy(array $criteria, array $orderBy = null)
 * @method VendorInstagramProfile[]    findAll()
 * @method VendorInstagramProfile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VendorInstagramProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VendorInstagramProfile::class);
    }

    public function save(VendorInstagramProfile $VendorInstagramProfile)
    {
        $this->getEntityManager()->persist($VendorInstagramProfile);
        $this->getEntityManager()->flush();
    }

    public function delete(VendorInstagramProfile $VendorInstagramProfile)
    {
        $this->getEntityManager()->remove($VendorInstagramProfile);
        $this->getEntityManager()->flush();
    }
}
