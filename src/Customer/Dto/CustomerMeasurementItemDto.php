<?php

namespace App\Customer\Dto;

class CustomerMeasurementItemDto
{
    public string $id;

    public MeasurementTypeDto $measurementType;

    public int $measurement;

    public string $unit;
}
