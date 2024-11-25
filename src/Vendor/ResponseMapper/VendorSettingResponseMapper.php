<?php

declare(strict_types=1);

namespace App\Vendor\ResponseMapper;

use App\Vendor\Dto\VendorSettingDto;
use App\Vendor\Entity\VendorSetting;

class VendorSettingResponseMapper
{
    public function map(VendorSetting $vendorSetting): VendorSettingDto
    {
        $vendorSettingDto = new VendorSettingDto();
        $vendorSettingDto->id = $vendorSetting->getId()
            ->toString();
        $vendorSettingDto->vendorId = $vendorSetting->getVendorId()
            ->toString();
        $vendorSettingDto->name = $vendorSetting->getName();
        $vendorSettingDto->value = $vendorSetting->getValue();

        return $vendorSettingDto;
    }

    /**
     * @return VendorSettingDto[]
     */
    public function mapMultiple(array $vendorSettings): array
    {
        $vendorSettingDtos = [];

        foreach ($vendorSettings as $vendorSetting) {
            $vendorSettingDtos[] = $this->map($vendorSetting);
        }

        return $vendorSettingDtos;
    }
}
