<?php

namespace App\Program\Message;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ProgramAssignmentExpiredEvent
{
    public const NAME = 'program_assignment.expired';

    protected ?UuidInterface $programAssignmentId = null;

    public function __construct(UuidInterface $programAssignmentId)
    {
        $this->programAssignmentId = $programAssignmentId;
    }

    public function getProgramAssignmentId(): ?UuidInterface
    {
        return $this->programAssignmentId;
    }
}