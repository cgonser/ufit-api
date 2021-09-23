<?php

declare(strict_types=1);

namespace App\Subscription\Request;

use Symfony\Component\Validator\Constraints\NotNull;
use App\Customer\Request\CustomerRequest;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints;

/**
 * @OA\RequestBody()
 */
class SubscriptionRequest
{
    /**
     * @OA\Property()
     */
    #[NotNull]
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
