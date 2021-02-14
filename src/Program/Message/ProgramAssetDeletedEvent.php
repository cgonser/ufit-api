<?php

namespace App\Program\Message;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ProgramAssetDeletedEvent
{
    public const NAME = 'program_asset.deleted';

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
