<?php

namespace App\Customer\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="CustomerMeasurementRequest"
 * )
 */
class CustomerMeasurementRequest
{
    /**
     * @OA\Property()
     */
    public ?string $notes = null;

    /**
     * @OA\Property()
     */
    public ?string $takenAt = null;
}
