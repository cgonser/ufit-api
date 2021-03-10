<?php

namespace App\Payment\Request;

use App\Customer\Request\CustomerRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="PaymentRequest"
 * )
 */
class PaymentRequest
{
    /**
     * @OA\Property()
     */
    public ?string $subscriptionCycleId = null;

    /**
     * @OA\Property()
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

    /**
     * @OA\Property()
     */
    public ?string $paymentMethodId = null;

    /**
     * @OA\Property(type="array", @OA\Items(type="object"))
     */
    public ?array $paymentDetails = null;
}
