<?php

namespace App\Core\Repository;

use App\Core\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CurrencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }

    public function save(Currency $currency)
    {
        $this->getEntityManager()->persist($currency);
        $this->getEntityManager()->flush();
    }

    public function delete(Currency $currency)
    {
        $this->getEntityManager()->remove($currency);
        $this->getEntityManager()->flush();
    }
}
