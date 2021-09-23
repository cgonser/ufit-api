<?php

declare(strict_types=1);

namespace App\Customer\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints;

/**
 * @OA\RequestBody()
 */
class CustomerMeasurementItemRequest extends AbstractRequest
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
    #[Constraints\Positive]
    public string $measurement;

    /**
     * @OA\Property()
     */
    public ?string $unit = null;
}
