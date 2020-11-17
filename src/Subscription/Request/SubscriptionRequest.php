<?php

namespace App\Subscription\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="SubscriptionRequest",
 *     required={"customerId", "vendorPlanId"},
 * )
 */
class SubscriptionRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $customerId = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $vendorPlanId = null;
}
