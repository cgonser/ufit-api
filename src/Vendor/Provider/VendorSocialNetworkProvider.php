<?php

namespace App\Vendor\Provider;

use App\Core\Provider\AbstractProvider;
use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorSocialNetwork;
use App\Vendor\Exception\VendorSocialNetworkNotFoundException;
use App\Vendor\Repository\VendorSocialNetworkRepository;

class VendorSocialNetworkProvider extends AbstractProvider
{
    public function __construct(VendorSocialNetworkRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findOneByVendorAndPlatform(Vendor $vendor, string $platform): ?VendorSocialNetwork
    {
        return $this->repository->findOneBy([
            'vendor' => $vendor,
            'platform' => $platform,
        ]);
    }

    public function findOneByExternalIdAndPlatform(string $externalId, string $platform): ?VendorSocialNetwork
    {
        return $this->repository->findOneBy([
            'externalId' => $externalId,
            'platform' => $platform,
        ]);
    }

    public function getByVendorAndPlatform(Vendor $vendor, string $platform): ?VendorSocialNetwork
    {
        $vendorSocialNetwork = $this->findOneByVendorAndPlatform($vendor, $platform);

        if (!$vendorSocialNetwork) {
            $this->throwNotFoundException();
        }

        return $vendorSocialNetwork;
    }

    protected function throwNotFoundException()
    {
        throw new VendorSocialNetworkNotFoundException();
    }
}
