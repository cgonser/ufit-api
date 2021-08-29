<?php

declare(strict_types=1);

namespace App\Vendor\ResponseMapper;

use App\Vendor\Dto\VendorStatsDto;
use App\Vendor\Dto\VendorStatsReceivableDto;
use App\Vendor\Dto\VendorStatsRevenueDto;
use App\Vendor\Dto\VendorStatsSubscriptionsDto;
use DateTime;
use DateTimeInterface;

class VendorStatsResponseMapper
{
    public function map(): VendorStatsDto
    {
        $vendorStatsReceivableDto = new VendorStatsReceivableDto();
        $vendorStatsReceivableDto->currentAmount = "500";
        $vendorStatsReceivableDto->nextPaymentAmount = "250";
        $vendorStatsReceivableDto->nextPaymentDate = (new DateTime())->format(DateTimeInterface::ATOM);

        $vendorStatsSubscriptionsDto = new VendorStatsSubscriptionsDto();
        $vendorStatsSubscriptionsDto->active = 10;
        $vendorStatsSubscriptionsDto->new = 3;
        $vendorStatsSubscriptionsDto->terminated = 1;

        $vendorStatsRevenueDto = new VendorStatsRevenueDto();
        $vendorStatsRevenueDto->currentAmount = "1000";
        $vendorStatsRevenueDto->previousAmount = "400";

        $vendorStatsDto = new VendorStatsDto();
        $vendorStatsDto->receivable = $vendorStatsReceivableDto;
        $vendorStatsDto->subscriptions = $vendorStatsSubscriptionsDto;
        $vendorStatsDto->revenue = $vendorStatsRevenueDto;

        return $vendorStatsDto;
    }
}
