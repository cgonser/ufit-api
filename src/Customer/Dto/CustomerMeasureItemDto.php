<?php

namespace App\Customer\Dto;

use OpenApi\Annotations as OA;

class CustomerMeasureItemDto
{
    public string $id;

    public string $customerMeasureId;

    public string $type;

    public string $measure;

    public string $unit;
}
