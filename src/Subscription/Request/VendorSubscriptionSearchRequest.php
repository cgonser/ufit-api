<?php

namespace App\Subscription\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="VendorSubscriptionSearchRequest"
 * )
 */
class VendorSubscriptionSearchRequest
{
    /**
     * @OA\Property()
     */
    public ?bool $isActive = null;

    /**
     * @OA\Property()
     */
    public ?bool $isInactive = null;

    /**
     * @OA\Property()
     */
    public ?bool $isPending = null;
}