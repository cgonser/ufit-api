<?php

declare(strict_types=1);

namespace App\Vendor\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints\Currency;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

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
     */
    #[Positive]
    public ?string $price;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $paymentMethods;

    /**
     * @OA\Property()
     */
    public ?string $currencyId;

    /**
     * @OA\Property()
     */
    #[Currency]
    public ?string $currency;

    /**
     * @OA\Property()
     */
    #[PositiveOrZero]
    public ?int $durationDays;

    /**
     * @OA\Property()
     */
    #[PositiveOrZero]
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
}
