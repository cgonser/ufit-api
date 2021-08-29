<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorGoogleLoginFailedException;
use App\Vendor\Provider\VendorProvider;
use Exception;
use Google_Client;

class VendorLoginGoogleManager
{
    public function __construct(
        private Google_Client $googleClient,
        private VendorProvider $vendorProvider,
        private VendorManager $vendorManager
    ) {
    }

    public function prepareVendorFromGoogleToken(string $accessToken): Vendor
    {
        try {
            $payload = $this->googleClient->verifyIdToken($accessToken);

            if (! $payload || ! isset($payload['email'])) {
                throw new VendorGoogleLoginFailedException();
            }

            $vendor = $this->vendorProvider->findOneByEmail($payload['email']);

            if (null === $vendor) {
                $vendor = $this->createVendorFromPayload($payload);
            }

            return $vendor;
        } catch (Exception) {
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
