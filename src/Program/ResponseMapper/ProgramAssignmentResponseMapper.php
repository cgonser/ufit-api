<?php

namespace App\Program\ResponseMapper;

use App\Customer\ResponseMapper\CustomerResponseMapper;
use App\Program\Dto\ProgramAssignmentDto;
use App\Program\Entity\ProgramAssignment;

class ProgramAssignmentResponseMapper
{
    private CustomerResponseMapper $customerResponseMapper;

    public function __construct(CustomerResponseMapper $customerResponseMapper)
    {
        $this->customerResponseMapper = $customerResponseMapper;
    }

    public function map(ProgramAssignment $programAssignment, bool $mapCustomer = true): ProgramAssignmentDto
    {
        $programAssignmentDto = new ProgramAssignmentDto();
        $programAssignmentDto->id = $programAssignment->getId()->toString();
        $programAssignmentDto->programId = $programAssignment->getProgram()->getId()->toString();
        $programAssignmentDto->assignedAt = $programAssignment->getAssignedAt()->format(\DateTimeInterface::ISO8601);
        $programAssignmentDto->expiresAt = null !== $programAssignment->getExpiresAt()
            ? $programAssignment->getExpiresAt()->format(\DateTimeInterface::ISO8601)
            : null;

        if ($mapCustomer) {
            $programAssignmentDto->customer = $this->customerResponseMapper->map($programAssignment->getCustomer());
        } else {
            $programAssignmentDto->customerId = $programAssignment->getCustomer()->getId()->toString();
        }

        return $programAssignmentDto;
    }

    public function mapMultiple(array $programAssignments, bool $mapCustomers = true): array
    {
        $dtos = [];

        foreach ($programAssignments as $programAssignment) {
            $dtos[] = $this->map($programAssignment, $mapCustomers);
        }

        return $dtos;
    }
}
