<?php

namespace App\Vendor\Provider;

use App\Core\Provider\AbstractProvider;
use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorSetting;
use App\Vendor\Exception\VendorSettingNotFoundException;
use App\Vendor\Repository\VendorSettingRepository;
use Ramsey\Uuid\UuidInterface;

class VendorSettingProvider extends AbstractProvider
{
    public function __construct(VendorSettingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getByVendorAndId(UuidInterface $vendorId, UuidInterface $vendorSettingId): VendorSetting
    {
        /** @var VendorSetting|null $vendorSetting */
        $vendorSetting = $this->repository->findOneBy([
            'id' => $vendorSettingId,
            'vendorId' => $vendorId,
        ]);

        if (!$vendorSetting) {
            $this->throwNotFoundException();
        }

        return $vendorSetting;
    }

    protected function throwNotFoundException()
    {
        throw new VendorSettingNotFoundException();
    }

    protected function getFilterableFields(): array
    {
        return [
            'vendorId',
        ];
    }
}
