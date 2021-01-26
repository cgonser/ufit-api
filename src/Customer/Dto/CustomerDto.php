<?php

namespace App\Customer\Dto;

use OpenApi\Annotations as OA;

class CustomerDto
{
    public string $id;

    public ?string $name;

    public ?string $email;

    public ?string $phone;

    public ?string $birthDate;

    public ?int $height;

    public ?string $gender;

    /**
     * @var string[]
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $goals;
}
