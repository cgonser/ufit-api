<?php

declare(strict_types=1);

namespace App\Subscription\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class SubscriptionSearchRequest extends SearchRequest
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
    public ?string $vendorPlanId = null;

    /**
     * @OA\Property()
     */
    public ?bool $isActive = null;

    /**
     * @OA\Property()
     */
    public ?bool $isPending = null;
}
