<?php

declare(strict_types=1);

namespace App\Customer\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="CustomerMeasurementItemRequest"
 * )
 */
class CustomerMeasurementItemRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     */
    public ?string $measurementTypeId;

    /**
     * @OA\Property()
     */
    public ?string $type;

    /**
     * @OA\Property()
     * @Assert\Positive()
     */
    public string $measurement;

    /**
     * @OA\Property()
     */
    public ?string $unit;
}
