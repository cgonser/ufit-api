<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="VendorPlanRequest",
 *     required={"name", "price", "currency"},
 * )
 */
class VendorPlanRequest
{
    /**
     * @OA\Property()
     */
    public ?string $name = null;

    /**
     * @OA\Property()
     */
    public ?string $slug = null;

    /**
     * @OA\Property()
     */
    public ?string $description = null;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $features = null;

    /**
     * @OA\Property()
     * @Assert\Positive()
     */
    public ?int $price = null;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $paymentMethods = null;

    /**
     * @OA\Property()
     * @Assert\Currency()
     */
    public ?string $currency = null;

    /**
     * @OA\Property()
     * @Assert\PositiveOrZero()
     */
    public ?int $durationDays = null;

    /**
     * @OA\Property()
     * @Assert\PositiveOrZero()
     */
    public ?int $durationMonths = null;

    /**
     * @OA\Property()
     */
    public ?string $questionnaireId = null;

    /**
     * @OA\Property()
     */
    public ?bool $isVisible = null;

    /**
     * @OA\Property()
     */
    public ?bool $isApprovalRequired = null;

    /**
     * @OA\Property()
     */
    public ?bool $isRecurring = null;

    /**
     * @OA\Property()
     */
    public ?string $imageContents = null;
}
