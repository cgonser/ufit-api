<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\VendorSetting;
use App\Vendor\Repository\VendorSettingRepository;
use Ramsey\Uuid\UuidInterface;

class VendorSettingManager
{
    private VendorSettingRepository $vendorSettingRepository;

    public function __construct(
        VendorSettingRepository $vendorSettingRepository
    ) {
        $this->vendorSettingRepository = $vendorSettingRepository;
    }

    public function create(VendorSetting $vendorSetting)
    {
        $this->vendorSettingRepository->save($vendorSetting);
    }

    public function update(VendorSetting $vendorSetting)
    {
        $this->vendorSettingRepository->save($vendorSetting);
    }

    public function delete(VendorSetting $vendorSetting)
    {
        $this->vendorSettingRepository->delete($vendorSetting);
    }

    public function set(UuidInterface $vendorId, string $name, string $value): VendorSetting
    {
        $vendorSetting = $this->get($vendorId, $name);

        if (!$vendorSetting) {
            $vendorSetting = new VendorSetting();
            $vendorSetting->setVendorId($vendorId);
            $vendorSetting->setName($name);
        }

        $vendorSetting->setValue($value);

        $this->vendorSettingRepository->save($vendorSetting);

        return $vendorSetting;
    }

    public function get(UuidInterface $vendorId, string $name): ?VendorSetting
    {
        return $this->vendorSettingRepository->findOneBy([
            'vendorId' => $vendorId,
            'name' => $name,
        ]);
    }

    public function getValue(UuidInterface $vendorId, string $name, ?string $defaultValue = null): ?string
    {
        $vendorSetting = $this->get($vendorId, $name);

        return $vendorSetting ? $vendorSetting->getValue() : $defaultValue;
    }
}
