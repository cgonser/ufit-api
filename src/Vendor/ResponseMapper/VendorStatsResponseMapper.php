<?php

namespace App\Vendor\ResponseMapper;

use App\Vendor\Dto\VendorStatsDto;
use App\Vendor\Dto\VendorStatsReceivableDto;
use App\Vendor\Dto\VendorStatsRevenueDto;
use App\Vendor\Dto\VendorStatsSubscriptionsDto;

class VendorStatsResponseMapper
{
    public function map(): VendorStatsDto
    {
        $vendorStatsReceivable = new VendorStatsReceivableDto();
        $vendorStatsReceivable->currentAmount = 500;
        $vendorStatsReceivable->nextPaymentAmount = 250;
        $vendorStatsReceivable->nextPaymentDate = (new \DateTime())->format(\DateTimeInterface::ATOM);

        $vendorStatsSubscriptionsDto = new VendorStatsSubscriptionsDto();
        $vendorStatsSubscriptionsDto->active = 10;
        $vendorStatsSubscriptionsDto->new = 3;
        $vendorStatsSubscriptionsDto->terminated = 1;

        $vendorStatsRevenueDto = new VendorStatsRevenueDto();
        $vendorStatsRevenueDto->currentAmount = 1000;
        $vendorStatsRevenueDto->previousAmount = 400;

        $vendorStatsDto = new VendorStatsDto();
        $vendorStatsDto->receivable = $vendorStatsReceivable;
        $vendorStatsDto->subscriptions = $vendorStatsSubscriptionsDto;
        $vendorStatsDto->revenue = $vendorStatsRevenueDto;

        return $vendorStatsDto;
    }
}
