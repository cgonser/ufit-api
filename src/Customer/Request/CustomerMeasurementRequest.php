<?php

namespace App\Customer\Request;

use Nelmio\ApiDocBundle\Annotation\Model;
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

    /**
     * @var CustomerMeasurementItemRequest[]
     *
     * @OA\Property(
     *     type="array",
     *     @OA\Items(ref=@Model(type=CustomerMeasurementItemRequest::class)))
     * )
     */
    public array $items = [];
}
