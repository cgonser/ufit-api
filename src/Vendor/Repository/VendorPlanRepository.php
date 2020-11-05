<?php

namespace App\Vendor\Repository;

use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VendorPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method VendorPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method VendorPlan[]    findAll()
 * @method VendorPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VendorPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VendorPlan::class);
    }

    public function findActivePlansByVendor(Vendor $vendor)
    {
        return $this->createQueryBuilder('vp')
            ->andWhere('vp.vendor = :vendor')
            ->setParameter('vendor', $vendor)
            ->orderBy('vp.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?VendorPlan
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
