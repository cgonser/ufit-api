<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="VendorPlanRequest",
 *     required={"name", "price", "currency", "durationDays", "durationMonths"},
 * )
 */
class VendorPlanRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $name = null;

    /**
     * @OA\Property()
     */
    public ?string $slug = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    public ?string $price = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     * @Assert\Currency()
     */
    public ?string $currency = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero()
     */
    public ?string $durationDays = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero()
     */
    public ?string $durationMonths = null;

    /**
     * @OA\Property()
     */
    public ?string $questionnaireId = null;
}
