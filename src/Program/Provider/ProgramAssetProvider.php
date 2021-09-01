<?php

declare(strict_types=1);

namespace App\Program\Provider;

use App\Core\Provider\AbstractProvider;
use App\Program\Entity\Program;
use App\Program\Entity\ProgramAsset;
use App\Program\Exception\ProgramAssetNotFoundException;
use App\Program\Repository\ProgramAssetRepository;
use Ramsey\Uuid\UuidInterface;

class ProgramAssetProvider extends AbstractProvider
{
    public function __construct(ProgramAssetRepository $programAssetRepository)
    {
        $this->repository = $programAssetRepository;
    }

    public function getByProgramAndId(Program $program, UuidInterface $programAssetId): ?ProgramAsset
    {
        /** @var ProgramAsset|null $programAsset */
        $programAsset = $this->repository->findOneBy([
            'id' => $programAssetId,
            'program' => $program,
        ]);

        if ($programAsset === null) {
            $this->throwNotFoundException();
        }

        return $programAsset;
    }

    protected function throwNotFoundException(): void
    {
        throw new ProgramAssetNotFoundException();
    }
}
