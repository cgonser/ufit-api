<?php

namespace App\Vendor\ResponseMapper;

use App\Core\ResponseMapper\PaymentMethodResponseMapper;
use App\Vendor\Dto\VendorSettingDto;
use App\Vendor\Entity\VendorSetting;
use Aws\S3\S3Client;

class VendorSettingResponseMapper
{
    public function map(VendorSetting $vendorSetting): VendorSettingDto
    {
        $vendorSettingDto = new VendorSettingDto();
        $vendorSettingDto->id = $vendorSetting->getId()->toString();
        $vendorSettingDto->vendorId = $vendorSetting->getVendor()->getId()->toString();
        $vendorSettingDto->name =  $vendorSetting->getName();
        $vendorSettingDto->value =  $vendorSetting->getValue();
        
        return $vendorSettingDto;
    }

    public function mapMultiple(array $vendorSettings): array
    {
        $vendorSettingDtos = [];

        foreach ($vendorSettings as $vendorSetting) {
            $vendorSettingDtos[] = $this->map($vendorSetting);
        }

        return $vendorSettingDtos;
    }
}
