<?php

declare(strict_types=1);

namespace App\Payment\Dto;

use OpenApi\Annotations as OA;

class PaymentMethodDto
{
    public string $id;

    public string $name;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $countriesEnabled = null;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $countriesDisabled = null;

    public bool $isActive;
}
