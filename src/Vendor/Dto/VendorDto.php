<?php

declare(strict_types=1);

namespace App\Vendor\Dto;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class VendorDto
{
    public string $id;

    public ?string $name = null;

    public ?string $displayName = null;

    public ?string $email = null;

    public ?string $slug = null;

    public ?string $photo = null;

    public ?string $biography = null;

    public ?string $country = null;

    public ?string $locale = null;

    public ?string $timezone = null;

    public ?bool $allowEmailMarketing = null;

    /**
     * @var VendorPlanDto[]
     * @OA\Property(type="array", @OA\Items(ref=@Model(type=VendorPlanDto::class)))
     */
    public ?array $plans = [];

    /**
     * @OA\Property(type="array", @OA\Items(type="object"))
     */
    public ?array $socialLinks = null;
}
