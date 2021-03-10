<?php

namespace App\Payment\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="PaymentRequest"
 * )
 */
class PaymentRequest
{
    /**
     * @OA\Property()
     * @Assert\NotNull
     */
    public ?string $invoiceId = null;

    /**
     * @OA\Property()
     * @Assert\NotNull
     */
    public ?string $paymentMethodId = null;

    /**
     * @OA\Property(type="array", @OA\Items(type="object"))
     */
    public ?array $details = null;
}
