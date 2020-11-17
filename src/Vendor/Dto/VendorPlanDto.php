<?php

namespace App\Vendor\Dto;

class VendorPlanDto
{
    public string $id;

    public string $vendorId;

    public ?string $questionnaireId = null;

    public string $name;

    public int $price;

    public string $currency;

    public string $durationMonths;

    public string $durationDays;
}