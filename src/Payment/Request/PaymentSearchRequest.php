<?php

declare(strict_types=1);

namespace App\Payment\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class PaymentSearchRequest extends SearchRequest
{
    /**
     * @OA\Property()
     */
    public ?string $vendorId = null;

    /**
     * @OA\Property()
     */
    public ?string $customerId = null;

    /**
     * @OA\Property()
     */
    public ?string $subscriptionId = null;
}
