<?php

declare(strict_types=1);

namespace App\Core\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function save($object): void
    {
        $this->getEntityManager()->persist($object);
        $this->getEntityManager()->flush();
    }

    public function delete($object): void
    {
        $this->getEntityManager()->remove($object);
        $this->getEntityManager()->flush();
    }
}
