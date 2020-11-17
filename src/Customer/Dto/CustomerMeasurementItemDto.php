<?php

namespace App\Customer\Dto;

use OpenApi\Annotations as OA;

class CustomerMeasurementItemDto
{
    public string $id;

    public string $customerMeasurementId;

    public string $type;

    public string $measurement;

    public string $unit;
}
