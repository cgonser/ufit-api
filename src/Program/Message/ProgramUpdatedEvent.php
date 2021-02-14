<?php

namespace App\Program\Message;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ProgramUpdatedEvent
{
    public const NAME = 'program.updated';

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
