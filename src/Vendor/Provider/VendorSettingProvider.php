<?php

declare(strict_types=1);

namespace App\Vendor\Provider;

use App\Core\Provider\AbstractProvider;
use App\Vendor\Entity\VendorSetting;
use App\Vendor\Exception\VendorSettingNotFoundException;
use App\Vendor\Repository\VendorSettingRepository;
use Ramsey\Uuid\UuidInterface;

class VendorSettingProvider extends AbstractProvider
{
    public function __construct(VendorSettingRepository $vendorSettingRepository)
    {
        $this->repository = $vendorSettingRepository;
    }

    public function getByVendorAndId(UuidInterface $vendorId, UuidInterface $vendorSettingId): ?VendorSetting
    {
        /** @var VendorSetting|null $vendorSetting */
        $vendorSetting = $this->repository->findOneBy([
            'id' => $vendorSettingId,
            'vendorId' => $vendorId,
        ]);

        if (null === $vendorSetting) {
            $this->throwNotFoundException();
        }

        return $vendorSetting;
    }

    protected function throwNotFoundException(): void
    {
        throw new VendorSettingNotFoundException();
    }

    /**
     * @return string[]
     */
    protected function getFilterableFields(): array
    {
        return ['vendorId'];
    }
}
