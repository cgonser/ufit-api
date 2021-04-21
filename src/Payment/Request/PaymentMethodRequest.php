<?php

namespace App\Payment\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="PaymentMethodRequest"
 * )
 */
class PaymentMethodRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $name = null;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $countriesEnabled = null;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $countriesDisabled = null;

    /**
     * @OA\Property()
     */
    public ?bool $isActive = null;
}
