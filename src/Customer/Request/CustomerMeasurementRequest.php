<?php

namespace App\Customer\Request;

use App\Core\Request\AbstractRequest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="CustomerMeasurementRequest"
 * )
 */
class CustomerMeasurementRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     */
    public ?string $notes;

    /**
     * @OA\Property()
     */
    public ?string $takenAt;

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
