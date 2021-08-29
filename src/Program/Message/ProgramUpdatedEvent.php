<?php

declare(strict_types=1);

namespace App\Program\Message;

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
