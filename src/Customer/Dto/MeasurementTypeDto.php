<?php

namespace App\Customer\Dto;

class MeasurementTypeDto
{
    public string $id;

    public string $name;

    public string $slug;

    public string $category;

    /**
     * @var string[]
     */
    public array $units;
}
