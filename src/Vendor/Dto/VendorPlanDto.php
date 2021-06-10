<?php

namespace App\Vendor\Dto;

use OpenApi\Annotations as OA;

class VendorPlanDto
{
    public string $id;

    public ?string $vendorId;

    public ?string $questionnaireId;

    public ?QuestionnaireDto $questionnaire;

    public string $name;

    public ?string $description;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $features;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $paymentMethods;

    public ?float $price = null;

    public bool $isVisible;

    public bool $isRecurring;

    public bool $isActive;

    public ?string $currency = null;

    public ?string $durationMonths = null;

    public ?string $durationDays = null;

    public ?string $image;
}
