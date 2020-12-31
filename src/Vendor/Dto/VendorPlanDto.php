<?php

namespace App\Vendor\Dto;

class VendorPlanDto
{
    public string $id;

    public ?string $vendorId;

    public ?string $questionnaireId;

    public ?QuestionnaireDto $questionnaire;

    public string $name;

    public ?string $description;

    public ?array $features;

    public int $price;

    public string $currency;

    public string $durationMonths;

    public string $durationDays;
}