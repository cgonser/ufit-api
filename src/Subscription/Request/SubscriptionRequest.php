<?php

namespace App\Subscription\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="SubscriptionRequest"
 * )
 */
class SubscriptionRequest
{
    /**
     * @OA\Property()
     */
    public ?string $vendorPlanId = null;
}
