<?php

declare(strict_types=1);

namespace App\Vendor\Dto;

use OpenApi\Annotations as OA;

class VendorPlanDto
{
    public string $id;

    public ?string $vendorId = null;

    public ?string $questionnaireId = null;

    public ?QuestionnaireDto $questionnaire = null;

    public string $name;

    public ?string $description = null;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $features = null;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $paymentMethods = null;

    public ?string $price = null;

    public bool $isVisible;

    public bool $isRecurring;

    public bool $isActive;

    public ?string $currency = null;

    public ?int $durationMonths = null;

    public ?int $durationDays = null;

    public ?string $image = null;
}
