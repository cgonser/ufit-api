<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorSocialNetwork;
use App\Vendor\Exception\VendorFacebookLoginFailedException;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Provider\VendorSocialNetworkProvider;
use App\Vendor\Request\VendorRequest;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphUser;

class VendorFacebookLoginService
{
    private Facebook $facebook;

    private VendorRequestManager $vendorRequestManager;

    private VendorSocialNetworkProvider $vendorSocialNetworkProvider;

    private VendorSocialNetworkManager $vendorSocialNetworkManager;

    public function __construct(
        Facebook $facebook,
        VendorRequestManager $vendorRequestManager,
        VendorSocialNetworkProvider $vendorSocialNetworkProvider,
        VendorSocialNetworkManager $vendorSocialNetworkManager
    ) {
        $this->facebook = $facebook;
        $this->vendorRequestManager = $vendorRequestManager;
        $this->vendorSocialNetworkProvider = $vendorSocialNetworkProvider;
        $this->vendorSocialNetworkManager = $vendorSocialNetworkManager;
    }

    public function prepareVendorFromFacebookToken(string $accessToken, ?string $ipAddress = null): Vendor
    {
        try {
            $response = $this->facebook->get('/me?fields=id,name,email', $accessToken);

            $graphUser = $response->getGraphUser();
            $vendor = $this->createOrUpdateVendorFromGraphUser($graphUser, $ipAddress);
            $this->createOrUpdateVendorSocialNetwork($vendor, $graphUser, $accessToken);

            return $vendor;
        } catch (FacebookResponseException | FacebookSDKException $e) {
            throw new VendorFacebookLoginFailedException();
        }
    }

    private function createOrUpdateVendorSocialNetwork(Vendor $vendor, array $graphUser, string $accessToken): void
    {
        $vendorSocialNetwork = $this->vendorSocialNetworkProvider->findOneByVendorAndPlatform(
            $vendor,
            VendorSocialNetwork::PLATFORM_FACEBOOK
        );

        if (!$vendorSocialNetwork) {
            $vendorSocialNetwork = new VendorSocialNetwork();
            $vendorSocialNetwork->setVendor($vendor);
            $vendorSocialNetwork->setExternalId($graphUser['id']);
            $vendorSocialNetwork->setPlatform(VendorSocialNetwork::PLATFORM_FACEBOOK);
        }

        $vendorSocialNetwork->setAccessToken($accessToken);
        $vendorSocialNetwork->setDetails($graphUser);

        $this->vendorSocialNetworkManager->save($vendorSocialNetwork);
    }

    private function createOrUpdateVendorFromGraphUser(GraphUser $graphUser, ?string $ipAddress = null): Vendor
    {
        $vendorSocialNetwork = $this->vendorSocialNetworkProvider->findOneByExternalIdAndPlatform(
            $graphUser->getId(),
            VendorSocialNetwork::PLATFORM_FACEBOOK
        );

        if ($vendorSocialNetwork) {
            return $vendorSocialNetwork->getVendor();
        }

        $vendorRequest = new VendorRequest();
        $vendorRequest->name = $graphUser->getName();
        $vendorRequest->displayName = $graphUser->getName();
        $vendorRequest->email = $graphUser->getEmail();

        return $this->vendorRequestManager->createFromRequest($vendorRequest, $ipAddress);
    }
}
