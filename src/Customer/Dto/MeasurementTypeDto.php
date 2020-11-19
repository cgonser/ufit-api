<?php

namespace App\Customer\Dto;

class MeasurementTypeDto
{
    public string $id;

    public string $name;

    public string $slug;

    /**
     * @var string[]
     */
    public array $units;
}
