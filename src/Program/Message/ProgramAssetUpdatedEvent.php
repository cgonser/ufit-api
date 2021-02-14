<?php

namespace App\Program\Message;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ProgramAssetUpdatedEvent
{
    public const NAME = 'program_asset.updated';

    protected ?UuidInterface $programAssetId = null;

    public function __construct(UuidInterface $programAssetId)
    {
        $this->programAssetId = $programAssetId;
    }

    public function getProgramAssetId(): ?UuidInterface
    {
        return $this->programAssetId;
    }
}
