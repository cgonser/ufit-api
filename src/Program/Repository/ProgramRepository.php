<?php

declare(strict_types=1);

namespace App\Program\Repository;

use App\Core\Repository\BaseRepository;
use App\Program\Entity\Program;
use Doctrine\Persistence\ManagerRegistry;

class ProgramRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Program::class);
    }
}
