<?php

declare(strict_types=1);

namespace App\Program\Message;

use Ramsey\Uuid\UuidInterface;

class ProgramDeletedEvent
{
    public const NAME = 'program.deleted';

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
