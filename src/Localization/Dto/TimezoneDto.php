<?php

declare(strict_types=1);

namespace App\Localization\Dto;

class TimezoneDto
{
    public ?string $name;

    public string $offsetGmt;

    public string $offsetRaw;
}
