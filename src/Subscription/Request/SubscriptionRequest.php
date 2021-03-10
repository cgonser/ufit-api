<?php

namespace App\Subscription\Request;

use App\Customer\Request\CustomerRequest;
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
     * @Assert\NotNull()
     */
    public ?string $vendorPlanId = null;

    /**
     * @OA\Property()
     */
    public ?CustomerRequest $customer = null;

    /**
     * @OA\Property()
     */
    public ?string $customerId = null;
}
