<?php

namespace App\Program\Dto;

class ProgramAssetDto
{
    public string $id;

    public string $programId;

    public ?string $title;

    public string $url;

    public ?string $type;

    public string $createdAt;
}
