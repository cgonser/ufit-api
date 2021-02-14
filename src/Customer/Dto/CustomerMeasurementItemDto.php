<?php

namespace App\Customer\Dto;

use Decimal\Decimal;

class CustomerMeasurementItemDto
{
    public string $id;

    public MeasurementTypeDto $measurementType;

    public Decimal $measurement;

    public string $unit;
}
