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
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class VendorFacebookLoginService
{
    private Facebook $facebook;

    private VendorProvider $vendorProvider;

    private VendorService $vendorService;

    private JWTTokenManagerInterface $JWTManager;

    public function __construct(
        Facebook $facebook,
        VendorProvider $vendorProvider,
        VendorService $vendorService,
        JWTTokenManagerInterface $JWTManager
    ) {
        $this->facebook = $facebook;
        $this->vendorProvider = $vendorProvider;
        $this->vendorService = $vendorService;
        $this->JWTManager = $JWTManager;
    }

    public function authenticateVendorFromToken(string $accessToken): string
    {
        try {
            $response = $this->facebook->get('/me?fields=id,name,email', $accessToken);

            $graphUser = $response->getGraphUser();

            $vendor = $this->vendorProvider->findOneByEmail($graphUser->getEmail());

            if (!$vendor) {
                $vendor = $this->createVendorFromGraphUser($graphUser);
            }

            return $this->JWTManager->create($vendor);
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
