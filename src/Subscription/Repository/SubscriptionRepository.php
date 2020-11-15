<?php

namespace App\Subscription\Repository;

use App\Subscription\Entity\Subscription;
use App\Customer\Entity\Customer;
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
            ->setParameter('customer', $customer)
            ->setParameter('expiresAt', $expiresAt)
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
