<?php

namespace App\Vendor\Dto;

class VendorPlanDto
{
    public string $id;

    public ?string $vendorId;

    public ?VendorDto $vendor;

    public ?string $questionnaireId;

    public ?QuestionnaireDto $questionnaire;

    public string $name;

    public int $price;

    public string $currency;

    public string $durationMonths;

    public string $durationDays;
}