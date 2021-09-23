<?php

declare(strict_types=1);

namespace App\Program\Dto;

use App\Vendor\Dto\VendorDto;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class ProgramDto
{
    public string $id;

    public ?VendorDto $vendor = null;

    public ?string $vendorId = null;

    public string $name;

    public ?string $level = null;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $goals = null;

    public ?string $description = null;

    public bool $isTemplate;

    public bool $isActive;

    /**
     * @var ProgramAssetDto[]
     *
     * @OA\Property(type="array", @OA\Items(ref=@Model(type=ProgramAssetDto::class)))
     */
    public ?array $assets = [];

    public ?string $createdAt = null;

    public ?string $updatedAt = null;
}
