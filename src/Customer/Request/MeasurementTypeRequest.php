<?php

namespace App\Customer\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="MeasurementTypeRequest"
 * )
 */
class MeasurementTypeRequest
{
    /**
     * @OA\Property()
     */
    public ?string $name = null;

    /**
     * @OA\Property()
     */
    public ?string $unit = null;

    /**
     * @OA\Property()
     */
    public ?string $category = null;
}
