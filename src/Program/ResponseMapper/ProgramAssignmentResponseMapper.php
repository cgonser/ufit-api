<?php

declare(strict_types=1);

namespace App\Program\ResponseMapper;

use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Program\Dto\ProgramAssignmentDto;
use App\Program\Entity\ProgramAssignment;

class ProgramAssignmentResponseMapper
{
    public function __construct(
        private CustomerResponseMapper $customerResponseMapper,
        private ProgramResponseMapper $programResponseMapper,
    ) {
    }

    public function map(
        ProgramAssignment $programAssignment,
        bool $mapCustomer = true,
        bool $mapProgram = false
    ): ProgramAssignmentDto {
        $programAssignmentDto = new ProgramAssignmentDto();
        $programAssignmentDto->id = $programAssignment->getId()->toString();
        $programAssignmentDto->createdAt = $programAssignment->getCreatedAt()->format(\DateTimeInterface::ATOM);
        $programAssignmentDto->updatedAt = $programAssignment->getUpdatedAt()?->format(\DateTimeInterface::ATOM);
        $programAssignmentDto->isActive = $programAssignment->isActive();
        $programAssignmentDto->expiresAt = $programAssignment->getExpiresAt()?->format(\DateTimeInterface::ATOM);

        if ($mapCustomer) {
            $programAssignmentDto->customer = $this->customerResponseMapper->map($programAssignment->getCustomer());
        } else {
            $programAssignmentDto->customerId = $programAssignment->getCustomer()->getId()->toString();
        }

        if ($mapProgram) {
            $programAssignmentDto->program = $this->programResponseMapper->map($programAssignment->getProgram());
        } else {
            $programAssignmentDto->programId = $programAssignment->getProgram()->getId()->toString();
        }

        return $programAssignmentDto;
    }

    /**
     * @return ProgramAssignmentDto[]
     */
    public function mapMultiple(array $programAssignments, bool $mapCustomers = true, bool $mapPrograms = false): array
    {
        $dtos = [];

        foreach ($programAssignments as $programAssignment) {
            $dtos[] = $this->map($programAssignment, $mapCustomers, $mapPrograms);
        }

        return $dtos;
    }
}
