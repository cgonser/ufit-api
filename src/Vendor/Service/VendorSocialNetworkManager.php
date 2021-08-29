<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Core\Validation\EntityValidator;
use App\Vendor\Entity\VendorSocialNetwork;
use App\Vendor\Repository\VendorSocialNetworkRepository;

class VendorSocialNetworkManager
{
    public function __construct(
        private VendorSocialNetworkRepository $vendorSocialNetworkRepository,
        private EntityValidator $entityValidator
    ) {
    }

    public function save(VendorSocialNetwork $vendorSocialNetwork): void
    {
        $this->entityValidator->validate($vendorSocialNetwork);

        $this->vendorSocialNetworkRepository->save($vendorSocialNetwork);
    }
}
