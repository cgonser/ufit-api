<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorFacebookLoginFailedException;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorRequest;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphUser;

class VendorFacebookLoginService
{
    private Facebook $facebook;

    private VendorProvider $vendorProvider;

    private VendorService $vendorService;

    public function __construct(
        Facebook $facebook,
        VendorProvider $vendorProvider,
        VendorService $vendorService
    ) {
        $this->facebook = $facebook;
        $this->vendorProvider = $vendorProvider;
        $this->vendorService = $vendorService;
    }

    public function prepareVendorFromFacebookToken(string $accessToken): Vendor
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

    private function createVendorFromGraphUser(GraphUser $graphUser): Vendor
    {
        $vendorRequest = new VendorRequest();
        $vendorRequest->name = $graphUser->getName();
        $vendorRequest->email = $graphUser->getEmail();

        return $this->vendorService->create($vendorRequest);
    }
}
