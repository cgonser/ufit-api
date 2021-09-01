<?php

declare(strict_types=1);

namespace App\Payment\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints;

/**
 * @OA\RequestBody()
 */
class PaymentMethodRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     */
    #[Constraints\NotBlank]
    public ?string $name;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $countriesEnabled;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $countriesDisabled;

    /**
     * @OA\Property()
     */
    public ?bool $isActive;
}
