<?php

namespace App\Payment\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="PaymentMethodSearchRequest"
 * )
 */
class PaymentMethodSearchRequest extends SearchRequest
{
    /**
     * @OA\Property()
     */
    public ?string $orderProperty = 'name';

    /**
     * @OA\Property()
     */
    public ?string $orderDirection = 'ASC';

    /**
     * @OA\Property()
     */
    public ?string $countryCode = null;
}