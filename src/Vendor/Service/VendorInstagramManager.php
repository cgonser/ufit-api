<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorInstagramProfile;
use App\Vendor\Exception\VendorInstagramLoginFailedException;
use App\Vendor\Exception\VendorInstagramLoginMissingEmailException;
use App\Vendor\Exception\VendorSlugInUseException;
use App\Vendor\Repository\VendorInstagramProfileRepository;
use App\Vendor\Request\VendorRequest;
use EspressoDev\InstagramBasicDisplay\InstagramBasicDisplay;
use EspressoDev\InstagramBasicDisplay\InstagramBasicDisplayException;
use Exception;
use InstagramScraper\Instagram;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class VendorInstagramManager implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private VendorRequestManager $vendorRequestManager,
        private VendorPhotoManager $vendorPhotoManager,
        private InstagramBasicDisplay $instagramBasicDisplay,
        private VendorInstagramProfileRepository $vendorInstagramProfileRepository,
        private Instagram $instagram
    ) {
    }

    public function prepareVendorFromInstagramCode(string $code, ?string $email = null): Vendor
    {
        try {
            $vendorInstagramProfile = $this->vendorInstagramProfileRepository->findOneBy([
                'code' => $code,
            ]);

            if (null === $vendorInstagramProfile) {
                $vendorInstagramProfile = new VendorInstagramProfile();
                $vendorInstagramProfile->setCode($code);
            }

            if (null === $vendorInstagramProfile->getAccessToken()) {
                $temporaryToken = $this->instagramBasicDisplay->getOAuthToken($code, true);
                $accessToken = $this->instagramBasicDisplay->getLongLivedToken($temporaryToken, true);

                if (null === $accessToken) {
                    throw new VendorInstagramLoginFailedException();
                }

                $vendorInstagramProfile->setAccessToken($accessToken);

                $this->vendorInstagramProfileRepository->save($vendorInstagramProfile);
            }

            $this->instagramBasicDisplay->setAccessToken($vendorInstagramProfile->getAccessToken());
            $this->instagramBasicDisplay->setUserFields('id,account_type,username');
            $profile = $this->instagramBasicDisplay->getUserProfile();

            if (null === $vendorInstagramProfile->getInstagramId()) {
                $vendorInstagramProfile->setInstagramId($profile->id);
                $vendorInstagramProfile->setUsername($profile->username);
                $vendorInstagramProfile->setIsBusiness('BUSINESS' === $profile->account_type);

                $this->vendorInstagramProfileRepository->save($vendorInstagramProfile);
            }

            if (null === $email) {
                throw new VendorInstagramLoginMissingEmailException();
            }

            return $this->createVendorFromInstagramProfileAndEmail($vendorInstagramProfile, $email);
        } catch (Exception | InstagramBasicDisplayException $e) {
            $this->logger->error($e->getMessage());

            throw new VendorInstagramLoginFailedException();
        }
    }

    public function updateVendorWithProfileData(VendorInstagramProfile $vendorInstagramProfile): void
    {
        $this->instagramBasicDisplay->setAccessToken($vendorInstagramProfile->getAccessToken());
        $this->instagramBasicDisplay->setUserFields('id,account_type,username');

        $profile = $this->instagramBasicDisplay->getUserProfile();

        $accountData = $this->instagram->getAccount($profile->username);

        $vendorRequest = new VendorRequest();
        $vendorRequest->name = $accountData->getFullName();
        $vendorRequest->displayName = $accountData->getFullName();
        $vendorRequest->biography = $accountData->getBiography();

        $this->vendorRequestManager->update($vendorInstagramProfile->getVendor(), $vendorRequest);

        if ('' !== $accountData->getProfilePicUrlHd() && '0' !== $accountData->getProfilePicUrlHd()) {
            $this->vendorPhotoManager->uploadFromUrl(
                $vendorInstagramProfile->getVendor(),
                $accountData->getProfilePicUrlHd()
            );
        }
    }

    private function createVendorFromInstagramProfileAndEmail(
        VendorInstagramProfile $vendorInstagramProfile,
        string $email
    ): Vendor {
        $vendorRequest = new VendorRequest();
        $vendorRequest->name = $vendorInstagramProfile->getUsername();
        $vendorRequest->email = $email;
        $vendorRequest->slug = $vendorInstagramProfile->getUsername();

        try {
            $accountData = $this->instagram->getAccount($vendorInstagramProfile->getUsername());

            $vendorRequest->name = $accountData->getFullName();
            $vendorRequest->displayName = $accountData->getFullName();
            $vendorRequest->biography = $accountData->getBiography();

            $photoUrl = $accountData->getProfilePicUrlHd() ?: null;
        } catch (Exception) {
            $photoUrl = null;
        }

        try {
            $vendor = $this->vendorRequestManager->createFromRequest($vendorRequest);
        } catch (VendorSlugInUseException) {
            $vendorRequest->slug = null;

            $vendor = $this->vendorRequestManager->createFromRequest($vendorRequest);
        }

        if (null !== $photoUrl) {
            $this->vendorPhotoManager->uploadFromUrl($vendor, $photoUrl);
        }

        $vendorInstagramProfile->setVendor($vendor);

        $this->vendorInstagramProfileRepository->save($vendorInstagramProfile);

        return $vendor;
    }
}
