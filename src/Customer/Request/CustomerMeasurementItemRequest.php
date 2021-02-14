<?php

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Positive()
     */
    public string $measurement;

    /**
     * @OA\Property()
     */
    public ?string $unit = null;

}
