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
     * @Assert\Uuid()
     */
    public ?string $invoiceId = null;

    /**
     * @OA\Property()
     * @Assert\NotNull
     */
    public ?string $paymentMethodId = null;

    /**
     * @OA\Property(
     *     type="object",
     *     @OA\Property(property="card_hash", type="string")
     * )
     */
    public ?array $details = null;
}
