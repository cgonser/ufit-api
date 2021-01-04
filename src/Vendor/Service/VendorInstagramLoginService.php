<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Provider\VendorProvider;

class VendorInstagramLoginService
{
    private VendorProvider $vendorProvider;

    private VendorService $vendorService;

    public function __construct(
        VendorProvider $vendorProvider,
        VendorService $vendorService,
    ) {
        $this->vendorProvider = $vendorProvider;
        $this->vendorService = $vendorService;
    }

    public function prepareVendorFromInstagramCode(string $code): Vendor
    {
        try {
            $response = $this->facebook->get('/me?fields=id,name,email', $accessToken);

            $graphUser = $response->getGraphUser();

            $vendor = $this->vendorProvider->findOneByEmail($graphUser->getEmail());

            if (!$vendor) {
                $vendor = $this->createVendorFromGraphUser($graphUser);
            }

            return $vendor;
        } catch (FacebookResponseException | FacebookSDKException $e) {
            throw new VendorFacebookLoginFailedException();
        }
    }
}