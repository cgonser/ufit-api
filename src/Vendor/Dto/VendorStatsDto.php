<?php

namespace App\Vendor\Dto;

class VendorStatsDto
{
    public ?VendorStatsRevenueDto $revenue;

    public ?VendorStatsReceivableDto $receivable;

    public ?VendorStatsSubscriptionsDto $subscriptions;
}
