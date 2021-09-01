<?php

declare(strict_types=1);

namespace App\Customer\Request;

use App\Core\Request\AbstractRequest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class CustomerMeasurementRequest extends AbstractRequest
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
     * @OA\Property(type="array", @OA\Items(ref=@Model(type=CustomerMeasurementItemRequest::class))))
     */
    public array $items = [];
}
