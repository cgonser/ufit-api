<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorGoogleLoginFailedException;
use App\Vendor\Provider\VendorProvider;
use Google_Client;

class VendorLoginGoogleManager
{
    private Google_Client $googleClient;

    private VendorProvider $vendorProvider;

    private VendorManager $vendorManager;

    public function __construct(
        Google_Client $googleClient,
        VendorProvider $vendorProvider,
        VendorManager $vendorManager
    ) {
        $this->googleClient = $googleClient;
        $this->vendorProvider = $vendorProvider;
        $this->vendorManager = $vendorManager;
    }

    public function prepareVendorFromGoogleToken(string $accessToken): Vendor
    {
        try {
            $payload = $this->googleClient->verifyIdToken($accessToken);

            if (!$payload || !isset($payload['email'])) {
                throw new VendorGoogleLoginFailedException();
            }

            $vendor = $this->vendorProvider->findOneByEmail($payload['email']);

            if (!$vendor) {
                $vendor = $this->createVendorFromPayload($payload);
            }

            return $vendor;
        } catch (\Exception $e) {
            throw new VendorGoogleLoginFailedException();
        }
    }

    private function createVendorFromPayload(array $payload): Vendor
    {
        $vendor = new Vendor();
        $vendor->setName($payload['name']);
        $vendor->setDisplayName($payload['name']);
        $vendor->setLocale($payload['locale']);
        $vendor->setEmail($payload['email']);

        $this->vendorManager->create($vendor);

        return $vendor;
    }
}
