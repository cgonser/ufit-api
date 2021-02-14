<?php

namespace App\Program\Dto;

use OpenApi\Annotations as OA;

class ProgramDto
{
    public string $id;

    public ?string $vendorId;

    public string $name;

    public ?string $level;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $goals;

    public bool $isTemplate;

    /**
     * @var ProgramAssetDto[]
     *
     * @OA\Property(type="array", @OA\Items(ref=@Model(type=ProgramAssetDto::class)))
     */
    public ?array $assets;

    public ?string $createdAt;

    public ?string $updatedAt;
}
