<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\VendorSetting;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorSettingRequest;
use Ramsey\Uuid\Uuid;

class VendorSettingRequestManager
{
    private VendorSettingManager $vendorSettingManager;

    private VendorProvider $vendorProvider;

    public function __construct(
        VendorSettingManager $vendorSettingManager,
        VendorProvider $vendorProvider
    ) {
        $this->vendorSettingManager = $vendorSettingManager;
        $this->vendorProvider = $vendorProvider;
    }

    public function createFromRequest(VendorSettingRequest $vendorSettingRequest): VendorSetting
    {
        $vendorSetting = new VendorSetting();

        $this->mapFromRequest($vendorSetting, $vendorSettingRequest);

        $this->vendorSettingManager->create($vendorSetting);

        return $vendorSetting;
    }

    public function updateFromRequest(
        VendorSetting $vendorSetting,
        VendorSettingRequest $vendorSettingRequest
    ) {
        $this->mapFromRequest($vendorSetting, $vendorSettingRequest);

        $this->vendorSettingManager->update($vendorSetting);
    }

    private function mapFromRequest(
        VendorSetting $vendorSetting,
        VendorSettingRequest $vendorSettingRequest
    ) {
        if (null !== $vendorSettingRequest->vendorId) {
            $vendor = $this->vendorProvider->get(Uuid::fromString($vendorSettingRequest->vendorId));

            $vendorSetting->setVendor($vendor);
        }

        if (null !== $vendorSettingRequest->name) {
            $vendorSetting->setName($vendorSettingRequest->name);
        }

        if (null !== $vendorSettingRequest->value) {
            $vendorSetting->setValue($vendorSettingRequest->value);
        }
    }
}
