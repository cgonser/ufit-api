<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Core\Service\FacebookApiClientFactory;
use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorSocialNetwork;
use App\Vendor\Exception\VendorFacebookLoginFailedException;
use App\Vendor\Provider\VendorSocialNetworkProvider;
use App\Vendor\Request\VendorRequest;

class VendorFacebookLoginService
{
    public function __construct(
        private FacebookApiClientFactory $facebookApiClientFactory,
        private VendorRequestManager $vendorRequestManager,
        private VendorSocialNetworkProvider $vendorSocialNetworkProvider,
        private VendorSocialNetworkManager $vendorSocialNetworkManager
    ) {
    }

    public function prepareVendorFromFacebookToken(string $accessToken, ?string $ipAddress = null): Vendor
    {
        try {
            $facebookApi = $this->facebookApiClientFactory->createInstance($accessToken);
            $response = $facebookApi->call('/me?fields=id,name,email,picture');

            $graphUser = $response->getContent();
            $vendor = $this->createOrUpdateVendorFromGraphUser($graphUser, $ipAddress);
            $this->createOrUpdateVendorSocialNetwork($vendor, $graphUser, $accessToken);

            return $vendor;
        } catch (\Exception $e) {
            throw new VendorFacebookLoginFailedException();
        }
    }

    private function createOrUpdateVendorSocialNetwork(
        Vendor $vendor,
        array $graphUser,
        string $accessToken
    ): void {
        $vendorSocialNetwork = $this->vendorSocialNetworkProvider->findOneByVendorAndPlatform(
            $vendor,
            VendorSocialNetwork::PLATFORM_FACEBOOK
        );

        if (null === $vendorSocialNetwork) {
            $vendorSocialNetwork = new VendorSocialNetwork();
            $vendorSocialNetwork->setVendor($vendor);
            $vendorSocialNetwork->setExternalId($graphUser['id']);
            $vendorSocialNetwork->setPlatform(VendorSocialNetwork::PLATFORM_FACEBOOK);
        }

        $vendorSocialNetwork->setAccessToken($accessToken);
        $vendorSocialNetwork->setDetails($graphUser);

        $this->vendorSocialNetworkManager->save($vendorSocialNetwork);
    }

    private function createOrUpdateVendorFromGraphUser(array $graphUser, ?string $ipAddress = null): Vendor
    {
        $vendorSocialNetwork = $this->vendorSocialNetworkProvider->findOneByExternalIdAndPlatform(
            $graphUser['id'],
            VendorSocialNetwork::PLATFORM_FACEBOOK
        );

        if (null !== $vendorSocialNetwork) {
            return $vendorSocialNetwork->getVendor();
        }

        $vendorRequest = new VendorRequest();
        $vendorRequest->name = $graphUser['name'];
        $vendorRequest->displayName = $graphUser['name'];
        $vendorRequest->email = $graphUser['email'];

        return $this->vendorRequestManager->createFromRequest($vendorRequest, $ipAddress);
    }
}
