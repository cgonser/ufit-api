<?php

declare(strict_types=1);

namespace App\Program\Dto;

use App\Customer\Dto\CustomerDto;

class ProgramAssignmentDto
{
    public string $id;

    public string $programId;

    public ?ProgramDto $program = null;

    public ?string $customerId = null;

    public ?CustomerDto $customer = null;

    public bool $isActive;

    public string $createdAt;

    public string $updatedAt;

    public ?string $expiresAt = null;
}
