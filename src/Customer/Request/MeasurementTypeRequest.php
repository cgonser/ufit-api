<?php

declare(strict_types=1);

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints;

/**
 * @OA\RequestBody()
 */
class MeasurementTypeRequest
{
    /**
     * @OA\Property()
     */
    #[Constraints\NotBlank]
    public ?string $name = null;

    /**
     * @var string[]
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    #[Constraints\NotBlank]
    public array $units = [];
}
