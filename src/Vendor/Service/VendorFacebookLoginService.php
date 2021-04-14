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

    private VendorRequestManager $vendorRequestManager;

    public function __construct(
        Facebook $facebook,
        VendorProvider $vendorProvider,
        VendorRequestManager $vendorRequestManager
    ) {
        $this->facebook = $facebook;
        $this->vendorProvider = $vendorProvider;
        $this->vendorRequestManager = $vendorRequestManager;
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
        $vendorRequest->displayName = $graphUser->getName();
        $vendorRequest->email = $graphUser->getEmail();

        return $this->vendorRequestManager->createFromRequest($vendorRequest);
    }
}
