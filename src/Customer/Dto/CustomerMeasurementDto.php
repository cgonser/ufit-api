<?php

namespace App\Customer\Dto;

use OpenApi\Annotations as OA;

class CustomerMeasurementDto
{
    public string $id;

    public string $notes;

    public string $takenAt;

    /**
     * @OA\Property(type="array", @OA\Items(type="CustomerMeasurementItemDto"))
     */
    public array $items = [];
}
