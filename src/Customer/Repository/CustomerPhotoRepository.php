<?php

declare(strict_types=1);

namespace App\Customer\Repository;

use App\Core\Repository\BaseRepository;
use App\Customer\Entity\CustomerPhoto;
use Doctrine\Persistence\ManagerRegistry;

class CustomerPhotoRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerPhoto::class);
    }
}
