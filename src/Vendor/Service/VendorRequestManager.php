<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorNotFoundException;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorPasswordChangeRequest;
use App\Vendor\Request\VendorPasswordResetRequest;
use App\Vendor\Request\VendorPasswordResetTokenRequest;
use App\Vendor\Request\VendorRequest;
use App\Vendor\Request\VendorSocialLinkRequest;
use GeoIp2\Database\Reader;
use Symfony\Component\Intl\Timezones;

class VendorRequestManager
{
    private VendorManager $vendorManager;

    private VendorProvider $vendorProvider;

    private VendorPhotoService $vendorPhotoService;

    private VendorPasswordManager $vendorPasswordManager;

    private Reader $geoIpReader;

    public function __construct(
        VendorManager $vendorManager,
        VendorProvider $vendorProvider,
        VendorPhotoService $vendorPhotoService,
        VendorPasswordManager $vendorPasswordManager,
        Reader $geoIpReader
    ) {
        $this->vendorManager = $vendorManager;
        $this->vendorProvider = $vendorProvider;
        $this->vendorPhotoService = $vendorPhotoService;
        $this->vendorPasswordManager = $vendorPasswordManager;
        $this->geoIpReader = $geoIpReader;
    }

    public function createFromRequest(VendorRequest $vendorRequest, ?string $ipAddress = null): Vendor
    {
        $vendor = new Vendor();

        $this->mapFromRequest($vendor, $vendorRequest);

        if (null !== $ipAddress) {
            $this->localizeVendor($vendor, $ipAddress);
        }

        $this->vendorManager->create($vendor);

        if ($vendorRequest->has('photoContents')) {
            $this->vendorPhotoService->uploadPhoto(
                $vendor,
                $this->vendorPhotoService->decodePhotoContents($vendorRequest->photoContents)
            );
        }

        return $vendor;
    }

    public function updateFromRequest(Vendor $vendor, VendorRequest $vendorRequest)
    {
        $this->mapFromRequest($vendor, $vendorRequest);

        $this->vendorManager->update($vendor);
    }

    public function mapFromRequest(Vendor $vendor, VendorRequest $vendorRequest)
    {
        if ($vendorRequest->has('email')) {
            $vendor->setEmail(strtolower($vendorRequest->email));
        }

        if ($vendorRequest->has('name')) {
            $vendor->setName($vendorRequest->name);
        }

        if ($vendorRequest->has('displayName')) {
            $vendor->setDisplayName($vendorRequest->displayName);
        }

        if (null === $vendor->getDisplayName()) {
            $vendor->setDisplayName($vendor->getName());
        }

        if ($vendorRequest->has('password') && null === $vendor->getPassword()) {
            $vendor->setPassword($this->vendorPasswordManager->encodePassword($vendor, $vendorRequest->password));
        }

        if ($vendorRequest->has('biography')) {
            $vendor->setBiography($vendorRequest->biography);
        }

        if ($vendorRequest->has('slug') && null !== $vendorRequest->slug) {
            $vendor->setSlug(strtolower($vendorRequest->slug));
        } elseif (null === $vendor->getSlug() && null !== $vendor->getDisplayName()) {
            $vendor->setSlug($this->vendorManager->generateSlug($vendor));
        }

        if ($vendorRequest->has('allowEmailMarketing')) {
            $vendor->setAllowEmailMarketing($vendorRequest->allowEmailMarketing);
        }

        if ($vendorRequest->has('socialLinks')) {
            $vendor->setSocialLinks($vendorRequest->socialLinks);
        }

        if ($vendorRequest->has('country')) {
            $vendor->setCountry($vendorRequest->country);
        }

        if ($vendorRequest->has('locale')) {
            $vendor->setLocale($vendorRequest->locale);
        }

        if ($vendorRequest->has('timezone')) {
            $vendor->setTimezone($vendorRequest->timezone);
        }
    }

    public function changePassword(Vendor $vendor, VendorPasswordChangeRequest $vendorPasswordChangeRequest)
    {
        $this->vendorPasswordManager->changePassword(
            $vendor,
            $vendorPasswordChangeRequest->currentPassword,
            $vendorPasswordChangeRequest->newPassword
        );
    }

    public function startPasswordReset(VendorPasswordResetRequest $vendorPasswordResetRequest)
    {
        $vendor = $this->vendorProvider->findOneByEmail($vendorPasswordResetRequest->emailAddress);

        if (!$vendor) {
            return;
        }

        $this->vendorPasswordManager->startPasswordReset($vendor);
    }

    public function concludePasswordReset(VendorPasswordResetTokenRequest $vendorPasswordResetTokenRequest)
    {
        [$emailAddress, $token] = explode('|', base64_decode($vendorPasswordResetTokenRequest->token));

        $vendor = $this->vendorProvider->findOneByEmail($emailAddress);

        if (!$vendor) {
            throw new VendorNotFoundException();
        }

        $this->vendorPasswordManager->resetPassword($vendor, $token, $vendorPasswordResetTokenRequest->password);
    }

    public function updateSocialLink(Vendor $vendor, VendorSocialLinkRequest $vendorSocialLinkRequest)
    {
        $vendor->setSocialLink($vendorSocialLinkRequest->network, $vendorSocialLinkRequest->link);

        $this->vendorManager->update($vendor);
    }

    private function localizeVendor(Vendor $vendor, string $ipAddress)
    {
        try {
            if (null === $vendor->getCountry()) {
                $record = $this->geoIpReader->country($ipAddress);

                $vendor->setCountry($record->country->isoCode);
            }

            if (null === $vendor->getTimezone()) {
                $vendor->setTimezone(Timezones::forCountryCode($vendor->getCountry())[0]);
            }
        } catch (\Exception $e) {
            // do nothing
        }
    }
}
