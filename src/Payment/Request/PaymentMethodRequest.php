<?php

declare(strict_types=1);

namespace App\Payment\Request;

use Symfony\Component\Validator\Constraints\NotBlank;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints;

/**
 * @OA\RequestBody(
 *     request="PaymentMethodRequest"
 * )
 */
class PaymentMethodRequest
{
    /**
     * @OA\Property()
     */
    #[NotBlank]
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
