<?php

declare(strict_types=1);

namespace App\Program\Message;

use Ramsey\Uuid\UuidInterface;

class ProgramAssetCreatedEvent
{
    public const NAME = 'program_asset.created';

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
