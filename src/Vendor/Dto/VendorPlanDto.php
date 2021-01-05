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

    public int $price;

    public bool $isVisible;

    public string $currency;

    public string $durationMonths;

    public string $durationDays;
}