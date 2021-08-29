<?php

declare(strict_types=1);

namespace App\Program\Dto;

use Nelmio\ApiDocBundle\Annotation\Model;
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

    public ?string $description;

    public bool $isTemplate;

    public bool $isActive;

    /**
     * @var ProgramAssetDto[]
     *
     * @OA\Property(type="array", @OA\Items(ref=@Model(type=ProgramAssetDto::class)))
     */
    public ?array $assets;

    public ?string $createdAt;

    public ?string $updatedAt;
}
