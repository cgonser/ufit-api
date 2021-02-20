<?php

namespace App\Program\Message;

use Ramsey\Uuid\UuidInterface;

class ProgramCreatedEvent
{
    public const NAME = 'program.created';

    protected ?UuidInterface $programId = null;

    public function __construct(UuidInterface $programId)
    {
        $this->programId = $programId;
    }

    public function getProgramId(): ?UuidInterface
    {
        return $this->programId;
    }
}
