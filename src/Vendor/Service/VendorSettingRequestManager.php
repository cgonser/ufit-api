<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Vendor\Entity\VendorSetting;
use App\Vendor\Request\VendorSettingRequest;
use Ramsey\Uuid\Uuid;

class VendorSettingRequestManager
{
    public function __construct(
        private VendorSettingManager $vendorSettingManager
    ) {
    }

    public function createFromRequest(VendorSettingRequest $vendorSettingRequest): VendorSetting
    {
        $vendorSetting = new VendorSetting();

        $this->mapFromRequest($vendorSetting, $vendorSettingRequest);

        $this->vendorSettingManager->create($vendorSetting);

        return $vendorSetting;
    }

    public function updateFromRequest(VendorSetting $vendorSetting, VendorSettingRequest $vendorSettingRequest): void
    {
        $this->mapFromRequest($vendorSetting, $vendorSettingRequest);

        $this->vendorSettingManager->update($vendorSetting);
    }

    private function mapFromRequest(VendorSetting $vendorSetting, VendorSettingRequest $vendorSettingRequest): void
    {
        if ($vendorSettingRequest->has('vendorId')) {
            $vendorSetting->setVendorId(Uuid::fromString($vendorSettingRequest->vendorId));
        }

        if ($vendorSettingRequest->has('name')) {
            $vendorSetting->setName($vendorSettingRequest->name);
        }

        if ($vendorSettingRequest->has('value')) {
            $vendorSetting->setValue($vendorSettingRequest->value);
        }
    }
}
