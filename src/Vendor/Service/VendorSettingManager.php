<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Vendor\Entity\VendorSetting;
use App\Vendor\Repository\VendorSettingRepository;
use Ramsey\Uuid\UuidInterface;

class VendorSettingManager
{
    public function __construct(
        private VendorSettingRepository $vendorSettingRepository
    ) {
    }

    public function create(VendorSetting $vendorSetting): void
    {
        $this->vendorSettingRepository->save($vendorSetting);
    }

    public function update(VendorSetting $vendorSetting): void
    {
        $this->vendorSettingRepository->save($vendorSetting);
    }

    public function delete(VendorSetting $vendorSetting): void
    {
        $this->vendorSettingRepository->delete($vendorSetting);
    }

    public function set(UuidInterface $vendorId, string $name, ?string $value): VendorSetting
    {
        $vendorSetting = $this->get($vendorId, $name);

        if (null === $vendorSetting) {
            $vendorSetting = new VendorSetting();
            $vendorSetting->setVendorId($vendorId);
            $vendorSetting->setName($name);
        }

        $vendorSetting->setValue($value);

        $this->vendorSettingRepository->save($vendorSetting);

        return $vendorSetting;
    }

    public function get(UuidInterface $vendorId, string $name): ?object
    {
        return $this->vendorSettingRepository->findOneBy([
            'vendorId' => $vendorId,
            'name' => $name,
        ]);
    }

    public function getValue(UuidInterface $vendorId, string $name, ?string $defaultValue = null): ?string
    {
        $vendorSetting = $this->get($vendorId, $name);

        return null !== $vendorSetting ? $vendorSetting->getValue() : $defaultValue;
    }
}
