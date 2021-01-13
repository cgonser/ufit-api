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

    private VendorService $vendorService;

    private VendorPhotoService $vendorPhotoService;

    private InstagramBasicDisplay $instagramBasicDisplay;

    private VendorInstagramProfileRepository $vendorInstagramProfileRepository;

    private Instagram $instagramScrapper;

    public function __construct(
        VendorProvider $vendorProvider,
        VendorService $vendorService,
        VendorPhotoService $vendorPhotoService,
        InstagramBasicDisplay $instagramBasicDisplay,
        VendorInstagramProfileRepository $vendorInstagramProfileRepository,
        Instagram $instagramScrapper
    ) {
        $this->vendorProvider = $vendorProvider;
        $this->vendorService = $vendorService;
        $this->vendorPhotoService = $vendorPhotoService;
        $this->instagramBasicDisplay = $instagramBasicDisplay;
        $this->vendorInstagramProfileRepository = $vendorInstagramProfileRepository;
        $this->instagramScrapper = $instagramScrapper;
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
            $this->logger->alert($e->getMessage());

            throw new VendorInstagramLoginFailedException();
        } catch (\Exception $e) {
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
        $vendorRequest->biography = $accountData->getBiography();

        $this->vendorService->update($vendorInstagramProfile->getVendor(), $vendorRequest);

        if ($accountData->getProfilePicUrlHd()) {
            $this->vendorPhotoService->uploadFromUrl($vendorInstagramProfile->getVendor(), $accountData->getProfilePicUrlHd());
        }
    }

    private function createVendorInstagramProfileAndEmail(\stdClass $profile, string $accessToken, string $email): VendorInstagramProfile
    {
        $vendorRequest = new VendorRequest();
        $vendorRequest->name = $profile->username;
        $vendorRequest->email = $email;
        $vendorRequest->slug = $profile->username;

        try {
            $accountData = $this->instagramScrapper->getAccount($profile->username);

            $vendorRequest->name = $accountData->getFullName();
            $vendorRequest->biography = $accountData->getBiography();

            $photoUrl = $accountData->getProfilePicUrlHd() ?: null;
        } catch (\Exception $e) {
            $photoUrl = null;
        }

        try {
            $vendor = $this->vendorService->create($vendorRequest);
        } catch (VendorSlugInUseException $e) {
            $vendorRequest->slug = null;

            $vendor = $this->vendorService->create($vendorRequest);
        }

        if (null !== $photoUrl) {
            $this->vendorPhotoService->uploadFromUrl($vendor, $photoUrl);
        }

        return $this->createVendorInstagramProfile($profile, $accessToken, $vendor);
    }

    private function createVendorInstagramProfile(\stdClass $profile, string $accessToken, Vendor $vendor): VendorInstagramProfile
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
