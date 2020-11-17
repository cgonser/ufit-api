<?php

namespace App\Customer\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="CustomerMeasurementItemRequest"
 * )
 */
class CustomerMeasurementItemRequest
{
    /**
     * @OA\Property()
     */
    public ?string $measurementTypeId = null;

    /**
     * @OA\Property()
     */
    public ?string $type = null;

    /**
     * @OA\Property()
     */
    public int $measurement;

    /**
     * @OA\Property()
     */
    public ?string $unit = null;

}
