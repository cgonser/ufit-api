<?php

namespace App\Subscription\Repository;

use App\Subscription\Entity\Subscription;
use App\Customer\Entity\Customer;
use App\Vendor\Entity\Vendor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function findActiveSubscriptionsByCustomer(Customer $customer)
    {
        $expiresAt = new \DateTime();

        return $this->createQueryBuilder('s')
            ->andWhere('s.customer = :customer')
            ->andWhere('s.expiresAt >= :expiresAt')
            ->andWhere('s.isApproved = true')
            ->setParameter('customer', $customer)
            ->setParameter('expiresAt', $expiresAt)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findActiveByVendor(Vendor $vendor)
    {
        $expiresAt = new \DateTime();

        return $this->createQueryBuilder('s')
            ->innerJoin('s.vendorPlan', 'vp')
            ->andWhere('vp.vendor = :vendor')
            ->andWhere('s.expiresAt >= :expiresAt')
            ->andWhere('s.isApproved = true')
            ->setParameter('vendor', $vendor)
            ->setParameter('expiresAt', $expiresAt)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findInactiveByVendor(Vendor $vendor)
    {
        $expiresAt = new \DateTime();

        return $this->createQueryBuilder('s')
            ->innerJoin('s.vendorPlan', 'vp')
            ->andWhere('vp.vendor = :vendor')
            ->andWhere('s.expiresAt < :expiresAt')
            ->andWhere('s.isApproved = true')
            ->setParameter('vendor', $vendor)
            ->setParameter('expiresAt', $expiresAt)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findPendingByVendor(Vendor $vendor)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.vendorPlan', 'vp')
            ->andWhere('vp.vendor = :vendor')
            ->andWhere('s.isApproved IS NULL')
            ->setParameter('vendor', $vendor)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByVendor(Vendor $vendor)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.vendorPlan', 'vp')
            ->andWhere('vp.vendor = :vendor')
            ->setParameter('vendor', $vendor)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function save(Subscription $subscription)
    {
        $this->getEntityManager()->persist($subscription);
        $this->getEntityManager()->flush();
    }

    public function delete(Subscription $subscription)
    {
        $this->getEntityManager()->remove($subscription);
        $this->getEntityManager()->flush();
    }
}
