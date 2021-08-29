<?php

declare(strict_types=1);

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="MeasurementTypeRequest"
 * )
 */
class MeasurementTypeRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $name = null;

    /**
     * @var string[]
     *
     * @OA\Property(
     *     type="array",
     *     @OA\Items(type="string")
     * )
     *
     * @Assert\NotBlank()
     */
    public array $units = [];
}
