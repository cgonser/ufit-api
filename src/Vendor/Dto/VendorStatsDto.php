<?php

declare(strict_types=1);

namespace App\Vendor\Dto;

class VendorStatsDto
{
    public ?VendorStatsRevenueDto $revenue = null;

    public ?VendorStatsReceivableDto $receivable = null;

    public ?VendorStatsSubscriptionsDto $subscriptions = null;
}
