<?php

namespace App\Customer\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class BillingInformationSearchRequest extends SearchRequest
{
    /**
     * @OA\Property()
     */
    public ?string $customerId = null;
}