<?php

declare(strict_types=1);

namespace App\Program\Provider;

use App\Core\Provider\AbstractProvider;
use App\Core\Request\SearchRequest;
use App\Program\Entity\Program;
use App\Program\Entity\ProgramAssignment;
use App\Program\Exception\ProgramAssignmentNotFoundException;
use App\Program\Repository\ProgramAssignmentRepository;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

class ProgramAssignmentProvider extends AbstractProvider
{
    public function __construct(ProgramAssignmentRepository $programAssignmentRepository)
    {
        $this->repository = $programAssignmentRepository;
    }

    public function getByProgramAndId(Program $program, UuidInterface $programAssignmentId): ?ProgramAssignment
    {
        /** @var ProgramAssignment|null $programAssignment */
        $programAssignment = $this->repository->findOneBy([
            'id' => $programAssignmentId,
            'program' => $program,
        ]);

        if ($programAssignment === null) {
            $this->throwNotFoundException();
        }

        return $programAssignment;
    }

    public function searchProgramAssignments(Program $program, SearchRequest $searchRequest): array
    {
        return $this->search($searchRequest, [
            'program' => $program,
        ]);
    }

    public function countProgramAssignments(Program $program, SearchRequest $searchRequest): int
    {
        return $this->count($searchRequest, [
            'program' => $program,
        ]);
    }

    protected function throwNotFoundException(): void
    {
        throw new ProgramAssignmentNotFoundException();
    }

    protected function buildQueryBuilder(): QueryBuilder
    {
        return parent::buildQueryBuilder()
            ->innerJoin('root.program', 'program');
    }

    protected function getSearchableFields(): array
    {
        return [];
    }

    /**
     * @return string[]|array<string, string>
     */
    protected function getFilterableFields(): array
    {
        return [
            'vendorId' => 'program',
            'customerId',
            'programId',
        ];
    }
}
