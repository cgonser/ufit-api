<?php

namespace App\Vendor\Dto;

use OpenApi\Annotations as OA;

class VendorDto
{
    public string $id;

    public string $name;

    public string $email;

    public string $slug;

    /**
     * @OA\Property(type="array", @OA\Items(type="VendorPlanDto"))
     */
    public array $plans;
}