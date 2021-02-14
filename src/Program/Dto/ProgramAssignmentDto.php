<?php

namespace App\Program\Dto;

use App\Customer\Dto\CustomerDto;

class ProgramAssignmentDto
{
    public string $id;

    public string $programId;

    public ?string $customerId;

    public ?CustomerDto $customer;

    public string $assignedAt;

    public ?string $expiresAt;
}
