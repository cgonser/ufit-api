<?php

namespace App\Vendor\Dto;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class VendorDto
{
    public string $id;

    public ?string $name;

    public ?string $displayName;

    public ?string $email;

    public ?string $slug;

    public ?string $photo;

    public ?string $biography;

    public ?string $country;

    public ?string $locale;

    public ?string $timezone;

    public ?bool $allowEmailMarketing;

    /**
     * @var VendorPlanDto[]
     * @OA\Property(type="array", @OA\Items(ref=@Model(type=VendorPlanDto::class)))
     */
    public ?array $plans;

    /**
     * @OA\Property(type="array", @OA\Items(type="object"))
     */
    public ?array $socialLinks;
}