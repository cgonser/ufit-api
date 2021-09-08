<?php

declare(strict_types=1);

namespace App\Subscription\Repository;

use App\Payment\Entity\Invoice;
use Doctrine\Common\Collections\Criteria;
use DateTime;
use App\Core\Repository\BaseRepository;
use App\Customer\Entity\Customer;
use App\Subscription\Entity\Subscription;
use App\Vendor\Entity\Vendor;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\UuidInterface;

class SubscriptionRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Subscription::class);
    }

    public function findCustomersByVendor(Vendor $vendor)
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->addSelect('s')
            ->from(Customer::class, 'c')
            ->innerJoin('c.subscriptions', 's')
            ->innerJoin('s.vendorPlan', 'vp')
            ->andWhere('vp.vendor = :vendor')
            ->setParameter('vendor', $vendor)
            ->orderBy('c.id', Criteria::ASC);

//            ->andWhere('s.isApproved = true')
//            ->andWhere('s.expiresAt >= :expiresAt')
//            ->setParameter('expiresAt', $expiresAt)

        return $queryBuilder->getQuery()
            ->getResult();
    }

    public function findOneVendorCustomer(Vendor $vendor, UuidInterface $customerId): Customer
    {
        $queryBuilder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->addSelect('s')
            ->from(Customer::class, 'c')
            ->innerJoin('c.subscriptions', 's')
            ->innerJoin('s.vendorPlan', 'vp')
            ->andWhere('vp.vendor = :vendor')
            ->andWhere('c.id = :customerId')
            ->setParameter('vendor', $vendor)
            ->setParameter('customerId', $customerId)
            ->orderBy('c.id', Criteria::ASC);

        return $queryBuilder->getQuery()
            ->getOneOrNullResult();
    }

    public function findActiveSubscriptionsByCustomer(Customer $customer)
    {
        $dateTime = new DateTime();

        return $this->createQueryBuilder('s')
            ->andWhere('s.customer = :customer')
            ->andWhere('s.expiresAt >= :expiresAt')
            ->andWhere('s.isApproved = true')
            ->setParameter('customer', $customer)
            ->setParameter('expiresAt', $dateTime)
            ->orderBy('s.id', Criteria::ASC)
            ->getQuery()
            ->getResult();
    }

    public function findActiveByVendor(UuidInterface $vendorId)
    {
        $dateTime = new DateTime();

        return $this->createQueryBuilder('s')
            ->innerJoin('s.vendorPlan', 'vp')
            ->andWhere('vp.vendorId = :vendorId')
            ->andWhere('s.expiresAt >= :expiresAt')
            ->andWhere('s.isApproved = true')
            ->andWhere('s.isActive = true')
            ->setParameter('vendorId', $vendorId)
            ->setParameter('expiresAt', $dateTime)
            ->orderBy('s.id', Criteria::ASC)
            ->getQuery()
            ->getResult();
    }

    public function findInactiveByVendor(UuidInterface $vendorId)
    {
        $dateTime = new DateTime();

        return $this->createQueryBuilder('s')
            ->innerJoin('s.vendorPlan', 'vp')
            ->andWhere('vp.vendorId = :vendorId')
            ->andWhere('s.expiresAt < :expiresAt')
            ->andWhere('s.isApproved = true')
            ->andWhere('s.isActive = false')
            ->setParameter('vendorId', $vendorId)
            ->setParameter('expiresAt', $dateTime)
            ->orderBy('s.id', Criteria::ASC)
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
            ->orderBy('s.id', Criteria::ASC)
            ->getQuery()
            ->getResult();
    }

    public function findByVendor(Vendor $vendor)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.vendorPlan', 'vp')
            ->andWhere('vp.vendor = :vendor')
            ->setParameter('vendor', $vendor)
            ->orderBy('s.id', Criteria::ASC)
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
            ->orderBy('s.id', Criteria::ASC)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getVendorReceivableStats(UuidInterface $vendorId): array
    {
        $paidInvoices = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('SUM(invoice.totalAmount) AS total_amount')
            ->from(Invoice::class, 'invoice')
            ->innerJoin('invoice.subscription', 'subscription')
            ->innerJoin('subscription.vendorPlan', 'vendorPlan')
            ->where('vendorPlan.vendorId = :vendorId')
            ->andWhere("invoice.paidAt < :referenceDate")
            ->setParameter('vendorId', $vendorId)
            ->setParameter('referenceDate', new \DateTime())
            ->getQuery()
            ->getOneOrNullResult();

        $nextInvoices = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('SUM(invoice.totalAmount) AS total_amount')
            ->from(Invoice::class, 'invoice')
            ->innerJoin('invoice.subscription', 'subscription')
            ->innerJoin('subscription.vendorPlan', 'vendorPlan')
            ->where('vendorPlan.vendorId = :vendorId')
            ->andWhere("invoice.paidAt IS NULL")
            ->setParameter('vendorId', $vendorId)
            ->getQuery()
            ->getOneOrNullResult();

        return [
            'currentAmount' => $paidInvoices['total_amount'] ?? 0,
            'nextPaymentAmount' => $nextInvoices['total_amount'] ?? 0,
        ];
    }
}
