<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorInstagramProfile;
use App\Vendor\Exception\VendorInstagramLoginFailedException;
use App\Vendor\Exception\VendorInstagramLoginMissingEmailException;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Repository\VendorInstagramProfileRepository;
use App\Vendor\Request\VendorRequest;
use EspressoDev\InstagramBasicDisplay\InstagramBasicDisplay;
use EspressoDev\InstagramBasicDisplay\InstagramBasicDisplayException;

class VendorInstagramLoginService
{
    private VendorProvider $vendorProvider;

    private VendorService $vendorService;

    private InstagramBasicDisplay $instagramBasicDisplay;

    private VendorInstagramProfileRepository $vendorInstagramProfileRepository;

    public function __construct(
        VendorProvider $vendorProvider,
        VendorService $vendorService,
        InstagramBasicDisplay $instagramBasicDisplay,
        VendorInstagramProfileRepository $vendorInstagramProfileRepository
    ) {
        $this->vendorProvider = $vendorProvider;
        $this->vendorService = $vendorService;
        $this->instagramBasicDisplay = $instagramBasicDisplay;
        $this->vendorInstagramProfileRepository = $vendorInstagramProfileRepository;
    }

    public function prepareVendorFromInstagramCode(string $code, ?string $email = null): Vendor
    {
        try {
            $temporaryToken = $this->instagramBasicDisplay->getOAuthToken($code, true);
            $accessToken = $this->instagramBasicDisplay->getLongLivedToken($temporaryToken, true);

            $this->instagramBasicDisplay->setAccessToken($accessToken);
            $this->instagramBasicDisplay->setUserFields('id,account_type,username');
            $profile = $this->instagramBasicDisplay->getUserProfile();

            $vendorInstagramProfile = $this->vendorInstagramProfileRepository->findOneBy(['instagramId' => $profile->id]);

            if (!$vendorInstagramProfile) {
                if (null === $email) {
                    throw new VendorInstagramLoginMissingEmailException();
                }

                $vendorInstagramProfile = $this->createVendorInstagramProfileAndEmail($profile, $accessToken, $email);
            }

            return $vendorInstagramProfile->getVendor();
        } catch (InstagramBasicDisplayException $e) {
            throw new VendorInstagramLoginFailedException();
        }
    }

    private function createVendorInstagramProfileAndEmail(\stdClass $profile, string $accessToken, string $email): VendorInstagramProfile
    {
        $vendorRequest = new VendorRequest();
        $vendorRequest->name = $profile->username;
        $vendorRequest->email = $email;

        $vendor = $this->vendorService->create($vendorRequest);

        return $this->createVendorInstagram($profile, $accessToken, $vendor);
    }

    private function createVendorInstagram(\stdClass $profile, string $accessToken, Vendor $vendor): VendorInstagramProfile
    {
        $vendorInstagramProfile = (new VendorInstagramProfile())
            ->setVendor($vendor)
            ->setInstagramId($profile->id)
            ->setUsername($profile->username)
            ->setAccessToken($accessToken)
            ->setIsBusiness('BUSINESS' == $profile->account_type);

        $this->vendorInstagramProfileRepository->save($vendorInstagramProfile);

        return $vendorInstagramProfile;
    }
}
