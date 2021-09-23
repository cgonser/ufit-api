<?php

declare(strict_types=1);

namespace App\Customer\Dto;

class CustomerMeasurementItemDto
{
    public string $id;

    public MeasurementTypeDto $measurementType;

    public string $measurement;

    public string $unit;
}
