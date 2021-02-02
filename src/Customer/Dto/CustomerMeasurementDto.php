<?php

namespace App\Customer\Dto;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class CustomerMeasurementDto
{
    public string $id;

    public string $notes;

    public string $takenAt;

    /**
     * @var CustomerMeasurementItemDto[]
     * @OA\Property(type="array", @OA\Items(ref=@Model(type=CustomerMeasurementItemDto::class)))
     */
    public array $items = [];
}
