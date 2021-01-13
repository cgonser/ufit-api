<?php

namespace App\Vendor\Dto;

use OpenApi\Annotations as OA;

class VendorDto
{
    public string $id;

    public ?string $name = null;

    public ?string $email = null;

    public ?string $slug = null;

    public ?string $photo = null;

    public ?string $biography = null;

    /**
     * @OA\Property(type="array", @OA\Items(type="VendorPlanDto"))
     */
    public array $plans;
}