<?php

namespace App\Vendor\ResponseMapper;

use App\Vendor\Dto\VendorPlanDto;
use App\Vendor\Entity\VendorPlan;

class VendorPlanResponseMapper
{
    public function map(VendorPlan $vendorPlan): VendorPlanDto
    {
        $vendorPlanDto = new VendorPlanDto();
        $vendorPlanDto->id = $vendorPlan->getId()->toString();
        $vendorPlanDto->vendorId = $vendorPlan->getVendor()->getId()->toString();
        $vendorPlanDto->name = $vendorPlan->getName() ?? '';
        $vendorPlanDto->currency = $vendorPlan->getCurrency()->getCode();
        $vendorPlanDto->durationDays = $vendorPlan->getDuration()->d;
        $vendorPlanDto->durationMonths = $vendorPlan->getDuration()->m;
        $vendorPlanDto->price = $vendorPlan->getPrice();

        return $vendorPlanDto;
    }

    public function mapMultiple(array $vendorPlans): array
    {
        $vendorPlanDtos = [];

        foreach ($vendorPlans as $vendorPlan) {
            $vendorPlanDtos[] = $this->map($vendorPlan);
        }

        return $vendorPlanDtos;
    }
}