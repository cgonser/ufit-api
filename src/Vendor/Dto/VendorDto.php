<?php

namespace App\Vendor\Dto;

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

    /**
     * @OA\Property(type="array", @OA\Items(type="VendorPlanDto"))
     */
    public ?array $plans;
}