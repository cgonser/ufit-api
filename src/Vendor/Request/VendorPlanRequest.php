<?php

namespace App\Vendor\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody()
 */
class VendorPlanRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     */
    public ?string $vendorId;

    /**
     * @OA\Property()
     */
    public ?string $name;

    /**
     * @OA\Property()
     */
    public ?string $slug;

    /**
     * @OA\Property()
     */
    public ?string $description;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $features;

    /**
     * @OA\Property()
     * @Assert\Positive()
     */
    public ?string $price;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $paymentMethods;

    /**
     * @OA\Property()
     * @Assert\Currency()
     */
    public ?string $currency;

    /**
     * @OA\Property()
     * @Assert\PositiveOrZero()
     */
    public ?int $durationDays;

    /**
     * @OA\Property()
     * @Assert\PositiveOrZero()
     */
    public ?int $durationMonths;

    /**
     * @OA\Property()
     */
    public ?string $questionnaireId;

    /**
     * @OA\Property()
     */
    public ?bool $isVisible;

    /**
     * @OA\Property()
     */
    public ?bool $isApprovalRequired;

    /**
     * @OA\Property()
     */
    public ?bool $isRecurring;

    /**
     * @OA\Property()
     */
    public ?bool $isActive;

    /**
     * @OA\Property()
     */
    public ?string $imageContents;
}
