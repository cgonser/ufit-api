<?php

namespace App\Program\ResponseMapper;

use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Program\Dto\ProgramAssignmentDto;
use App\Program\Entity\ProgramAssignment;

class ProgramAssignmentResponseMapper
{
    private CustomerResponseMapper $customerResponseMapper;

    private ProgramResponseMapper $programResponseMapper;

    public function __construct(
        CustomerResponseMapper $customerResponseMapper,
        ProgramResponseMapper $programResponseMapper
    ) {
        $this->customerResponseMapper = $customerResponseMapper;
        $this->programResponseMapper = $programResponseMapper;
    }

    public function map(
        ProgramAssignment $programAssignment,
        bool $mapCustomer = true,
        bool $mapProgram = false
    ): ProgramAssignmentDto {
        $programAssignmentDto = new ProgramAssignmentDto();
        $programAssignmentDto->id = $programAssignment->getId()->toString();
        $programAssignmentDto->createdAt = $programAssignment->getCreatedAt()->format(\DateTimeInterface::ISO8601);
        $programAssignmentDto->updatedAt = $programAssignment->getUpdatedAt()->format(\DateTimeInterface::ISO8601);
        $programAssignmentDto->isActive = $programAssignment->isActive();
        $programAssignmentDto->expiresAt = null !== $programAssignment->getExpiresAt()
            ? $programAssignment->getExpiresAt()->format(\DateTimeInterface::ISO8601)
            : null;

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

    public function mapMultiple(array $programAssignments, bool $mapCustomers = true, bool $mapPrograms = false): array
    {
        $dtos = [];

        foreach ($programAssignments as $programAssignment) {
            $dtos[] = $this->map($programAssignment, $mapCustomers, $mapPrograms);
        }

        return $dtos;
    }
}
