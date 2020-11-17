<?php

namespace App\Customer\Dto;

use OpenApi\Annotations as OA;

class CustomerMeasurementItemDto
{
    public string $id;

    /**
     * @var MeasurementTypeDto[]
     * @OA\Property(type="array", @OA\Items(type="MeasurementTypeDto"))
     */
    public MeasurementTypeDto $measurementType;

    public int $measurement;

    public string $unit;
}
