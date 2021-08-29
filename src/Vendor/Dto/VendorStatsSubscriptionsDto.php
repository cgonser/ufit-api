<?php

declare(strict_types=1);

namespace App\Vendor\Dto;

class VendorStatsSubscriptionsDto
{
    public ?int $active = 0;

    public ?int $new = 0;

    public ?int $terminated = 0;
}
