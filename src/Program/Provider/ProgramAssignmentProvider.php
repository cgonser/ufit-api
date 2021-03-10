<?php

namespace App\Program\Provider;

use App\Core\Provider\AbstractProvider;
use App\Core\Request\SearchRequest;
use App\Program\Entity\Program;
use App\Program\Entity\ProgramAssignment;
use App\Program\Exception\ProgramAssignmentNotFoundException;
use App\Program\Repository\ProgramAssignmentRepository;
use Ramsey\Uuid\UuidInterface;

class ProgramAssignmentProvider extends AbstractProvider
{
    public function __construct(ProgramAssignmentRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function throwNotFoundException()
    {
        throw new ProgramAssignmentNotFoundException();
    }

    public function getByProgramAndId(Program $program, UuidInterface $programAssignmentId): ProgramAssignment
    {
        /** @var ProgramAssignment|null $programAssignment */
        $programAssignment = $this->repository->findOneBy([
            'id' => $programAssignmentId,
            'program' => $program,
        ]);

        if (!$programAssignment) {
            $this->throwNotFoundException();
        }

        return $programAssignment;
    }

    public function searchProgramAssignments(Program $program, SearchRequest $searchRequest): array
    {
        return $this->search($searchRequest, ['program' => $program]);
    }

    public function countProgramAssignments(Program $program, SearchRequest $searchRequest): int
    {
        return $this->count($searchRequest, ['program' => $program]);
    }

    protected function getSearchableFields(): array
    {
        return [];
    }

    protected function getFilterableFields(): array
    {
        return [
            'customerId',
            'programId',
        ];
    }
}
