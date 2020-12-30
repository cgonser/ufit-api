<?php

namespace App\Vendor\ResponseMapper;

use App\Vendor\Dto\VendorDto;
use App\Vendor\Entity\Vendor;

class VendorResponseMapper
{
    private VendorPlanResponseMapper $vendorPlanResponseMapper;

    public function __construct(VendorPlanResponseMapper $vendorPlanResponseMapper)
    {
        $this->vendorPlanResponseMapper = $vendorPlanResponseMapper;
    }

    public function map(Vendor $vendor): VendorDto
    {
        $vendorDto = new VendorDto();
        $vendorDto->id = $vendor->getId()->toString();
        $vendorDto->name = $vendor->getName() ?? '';
        $vendorDto->slug = $vendor->getSlug() ?? '';
        // $vendorDto->email = $vendor->getEmail() ?? '';

        $vendorDto->plans = $this->vendorPlanResponseMapper->mapMultiple($vendor->getPlans()->toArray());

        return $vendorDto;
    }

    public function mapMultiple(array $vendors): array
    {
        $vendorDtos = [];

        foreach ($vendors as $vendor) {
            $vendorDtos[] = $this->map($vendor);
        }

        return $vendorDtos;
    }
}