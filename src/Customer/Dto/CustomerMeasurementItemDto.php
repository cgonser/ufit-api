<?php

namespace App\Customer\Dto;

class CustomerMeasurementItemDto
{
    public string $id;

    public MeasurementTypeDto $measurementType;

    public float $measurement;

    public string $unit;
}
