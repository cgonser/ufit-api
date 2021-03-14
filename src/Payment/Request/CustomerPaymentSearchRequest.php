<?php

namespace App\Payment\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="CustomerPaymentSearchRequest"
 * )
 */
class CustomerPaymentSearchRequest extends SearchRequest
{
    /**
     * @OA\Property()
     */
    public ?string $customerId = null;

    /**
     * @OA\Property()
     */
    public ?string $subscriptionId = null;
}