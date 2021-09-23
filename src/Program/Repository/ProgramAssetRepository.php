<?php

declare(strict_types=1);

namespace App\Program\Repository;

use App\Core\Repository\BaseRepository;
use App\Program\Entity\ProgramAsset;
use Doctrine\Persistence\ManagerRegistry;

class ProgramAssetRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, ProgramAsset::class);
    }
}
