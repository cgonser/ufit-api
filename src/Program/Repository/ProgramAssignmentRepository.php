<?php

namespace App\Program\Repository;

use App\Core\Repository\BaseRepository;
use App\Program\Entity\ProgramAssignment;
use Doctrine\Persistence\ManagerRegistry;

class ProgramAssignmentRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProgramAssignment::class);
    }
}
