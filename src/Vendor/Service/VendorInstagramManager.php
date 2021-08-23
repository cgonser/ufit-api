<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorInstagramProfile;
use App\Vendor\Exception\VendorInstagramLoginFailedException;
use App\Vendor\Exception\VendorInstagramLoginMissingEmailException;
use App\Vendor\Exception\VendorSlugInUseException;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Repository\VendorInstagramProfileRepository;
use App\Vendor\Request\VendorRequest;
use EspressoDev\InstagramBasicDisplay\InstagramBasicDisplay;
use EspressoDev\InstagramBasicDisplay\InstagramBasicDisplayException;
use InstagramScraper\Instagram;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class VendorInstagramManager implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private VendorProvider $vendorProvider;

    private VendorRequestManager $vendorRequestManager;

    private VendorPhotoManager $vendorPhotoManager;

    private InstagramBasicDisplay $instagramBasicDisplay;

    private VendorInstagramProfileRepository $vendorInstagramProfileRepository;

    private Instagram $instagramScrapper;

    public function __construct(
        VendorProvider $vendorProvider,
        VendorRequestManager $vendorRequestManager,
        VendorPhotoManager $vendorPhotoManager,
        InstagramBasicDisplay $instagramBasicDisplay,
        VendorInstagramProfileRepository $vendorInstagramProfileRepository,
        Instagram $instagramScrapper
    ) {
        $this->vendorProvider = $vendorProvider;
        $this->vendorRequestManager = $vendorRequestManager;
        $this->vendorPhotoManager = $vendorPhotoManager;
        $this->instagramBasicDisplay = $instagramBasicDisplay;
        $this->vendorInstagramProfileRepository = $vendorInstagramProfileRepository;
        $this->instagramScrapper = $instagramScrapper;
    }

    public function prepareVendorFromInstagramCode(string $code, ?string $email = null): Vendor
    {
        try {
            $vendorInstagramProfile = $this->vendorInstagramProfileRepository->findOneBy(['code' => $code]);

            if (!$vendorInstagramProfile) {
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
                $vendorInstagramProfile->setIsBusiness('BUSINESS' == $profile->account_type);

                $this->vendorInstagramProfileRepository->save($vendorInstagramProfile);
            }

            if (null === $email) {
                throw new VendorInstagramLoginMissingEmailException();
            }

            return $this->createVendorFromInstagramProfileAndEmail($vendorInstagramProfile, $email);
        } catch (\Exception | InstagramBasicDisplayException $e) {
            $this->logger->error($e->getMessage());

            throw new VendorInstagramLoginFailedException();
        }
    }

    public function updateVendorWithProfileData(VendorInstagramProfile $vendorInstagramProfile)
    {
        $this->instagramBasicDisplay->setAccessToken($vendorInstagramProfile->getAccessToken());
        $this->instagramBasicDisplay->setUserFields('id,account_type,username');
        $profile = $this->instagramBasicDisplay->getUserProfile();

        $accountData = $this->instagramScrapper->getAccount($profile->username);

        $vendorRequest = new VendorRequest();
        $vendorRequest->name = $accountData->getFullName();
        $vendorRequest->displayName = $accountData->getFullName();
        $vendorRequest->biography = $accountData->getBiography();

        $this->vendorRequestManager->update($vendorInstagramProfile->getVendor(), $vendorRequest);

        if ($accountData->getProfilePicUrlHd()) {
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
            $accountData = $this->instagramScrapper->getAccount($vendorInstagramProfile->getUsername());

            $vendorRequest->name = $accountData->getFullName();
            $vendorRequest->displayName = $accountData->getFullName();
            $vendorRequest->biography = $accountData->getBiography();

            $photoUrl = $accountData->getProfilePicUrlHd() ?: null;
        } catch (\Exception $e) {
            $photoUrl = null;
        }

        try {
            $vendor = $this->vendorRequestManager->createFromRequest($vendorRequest);
        } catch (VendorSlugInUseException $e) {
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

    private function createVendorInstagramProfile(
        \stdClass $profile,
        string $accessToken,
        Vendor $vendor
    ): VendorInstagramProfile {
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
