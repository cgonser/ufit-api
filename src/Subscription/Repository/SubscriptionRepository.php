<?php

namespace App\Subscription\Repository;

use App\Core\Repository\BaseRepository;
use App\Customer\Entity\Customer;
use App\Subscription\Entity\Subscription;
use App\Vendor\Entity\Vendor;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

class SubscriptionRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function findCustomersByVendor(Vendor $vendor)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->addSelect('s')
            ->from(Customer::class, 'c')
            ->innerJoin('c.subscriptions', 's')
            ->innerJoin('s.vendorPlan', 'vp')
            ->andWhere('vp.vendor = :vendor')
            ->setParameter('vendor', $vendor)
            ->orderBy('c.id', 'ASC');

//            ->andWhere('s.isApproved = true')
//            ->andWhere('s.expiresAt >= :expiresAt')
//            ->setParameter('expiresAt', $expiresAt)

        return $query->getQuery()->getResult();
    }

    public function findOneVendorCustomer(Vendor $vendor, UuidInterface $customerId): Customer
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->addSelect('s')
            ->from(Customer::class, 'c')
            ->innerJoin('c.subscriptions', 's')
            ->innerJoin('s.vendorPlan', 'vp')
            ->andWhere('vp.vendor = :vendor')
            ->andWhere('c.id = :customerId')
            ->setParameter('vendor', $vendor)
            ->setParameter('customerId', $customerId)
            ->orderBy('c.id', 'ASC');

        return $query->getQuery()->getOneOrNullResult();
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
            ->getResult();
    }

    public function findActiveByVendor(Vendor $vendor)
    {
        $expiresAt = new \DateTime();

        return $this->createQueryBuilder('s')
            ->innerJoin('s.vendorPlan', 'vp')
            ->andWhere('vp.vendor = :vendor')
            ->andWhere('s.expiresAt >= :expiresAt')
            ->andWhere('s.isApproved = true')
            ->andWhere('s.isActive = true')
            ->setParameter('vendor', $vendor)
            ->setParameter('expiresAt', $expiresAt)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findInactiveByVendor(Vendor $vendor)
    {
        $expiresAt = new \DateTime();

        return $this->createQueryBuilder('s')
            ->innerJoin('s.vendorPlan', 'vp')
            ->andWhere('vp.vendor = :vendor')
            ->andWhere('s.expiresAt < :expiresAt')
            ->andWhere('s.isApproved = true')
            ->andWhere('s.isActive = false')
            ->setParameter('vendor', $vendor)
            ->setParameter('expiresAt', $expiresAt)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();
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
            ->getResult();
    }

    public function findByVendor(Vendor $vendor)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.vendorPlan', 'vp')
            ->andWhere('vp.vendor = :vendor')
            ->setParameter('vendor', $vendor)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByVendorAndId(Vendor $vendor, UuidInterface $subscriptionId): ?Subscription
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.vendorPlan', 'vp')
            ->andWhere('vp.vendor = :vendor')
            ->andWhere('s.id = :subscriptionId')
            ->setParameter('vendor', $vendor)
            ->setParameter('subscriptionId', $subscriptionId)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
