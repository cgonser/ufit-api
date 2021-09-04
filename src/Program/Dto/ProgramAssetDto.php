<?php

declare(strict_types=1);

namespace App\Program\Dto;

class ProgramAssetDto
{
    public string $id;

    public string $programId;

    public ?string $title = null;

    public string $url;

    public ?string $type = null;

    public string $createdAt;
}
