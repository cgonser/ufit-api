<?php

namespace App\Vendor\Service;

use App\Core\Validation\EntityValidator;
use App\Vendor\Entity\VendorSocialNetwork;
use App\Vendor\Repository\VendorSocialNetworkRepository;

class VendorSocialNetworkManager
{
    private VendorSocialNetworkRepository $vendorSocialNetworkRepository;

    private EntityValidator $validator;

    public function __construct(
        VendorSocialNetworkRepository $vendorSocialNetworkRepository,
        EntityValidator $validator
    ) {
        $this->vendorSocialNetworkRepository = $vendorSocialNetworkRepository;
        $this->validator = $validator;
    }

    public function save(VendorSocialNetwork $vendorSocialNetwork)
    {
        $this->validator->validate($vendorSocialNetwork);

        $this->vendorSocialNetworkRepository->save($vendorSocialNetwork);
    }
}
