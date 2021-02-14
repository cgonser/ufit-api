<?php

namespace App\Program\Provider;

use App\Core\Provider\AbstractProvider;
use App\Core\Request\SearchRequest;
use App\Program\Entity\Program;
use App\Program\Exception\ProgramNotFoundException;
use App\Program\Repository\ProgramRepository;
use App\Vendor\Entity\Vendor;
use Ramsey\Uuid\UuidInterface;

class ProgramProvider extends AbstractProvider
{
    public function __construct(ProgramRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function throwNotFoundException()
    {
        throw new ProgramNotFoundException();
    }

    public function getByVendorAndId(Vendor $vendor, UuidInterface $programId): Program
    {
        /** @var Program|null $program */
        $program = $this->repository->findOneBy([
            'id' => $programId,
            'vendor' => $vendor,
        ]);

        if (!$program) {
            $this->throwNotFoundException();
        }

        return $program;
    }

    public function findByVendor(Vendor $vendor): array
    {
        return $this->repository->findBy(['vendor' => $vendor]);
    }

    public function searchVendorPrograms(Vendor $vendor, SearchRequest $searchRequest): array
    {
        return $this->search($searchRequest, ['vendor' => $vendor]);
    }

    public function countVendorPrograms(Vendor $vendor, SearchRequest $searchRequest): int
    {
        return $this->count($searchRequest, ['vendor' => $vendor]);
    }

    protected function getSearchableFields(): array
    {
        return [
            'name' => 'text',
            'level' => 'text',
        ];
    }
}
