<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorNotFoundException;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Request\VendorPasswordChangeRequest;
use App\Vendor\Request\VendorPasswordResetRequest;
use App\Vendor\Request\VendorPasswordResetTokenRequest;
use App\Vendor\Request\VendorRequest;
use App\Vendor\Request\VendorSocialLinkRequest;
use Exception;
use GeoIp2\Database\Reader;
use Symfony\Component\Intl\Timezones;

class VendorRequestManager
{
    public function __construct(
        private VendorManager $vendorManager,
        private VendorProvider $vendorProvider,
        private VendorPasswordManager $vendorPasswordManager,
        private Reader $geoIpReader
    ) {
    }

    public function createFromRequest(VendorRequest $vendorRequest, ?string $ipAddress = null): Vendor
    {
        $vendor = new Vendor();

        $this->mapFromRequest($vendor, $vendorRequest);

        if (null !== $ipAddress) {
            $this->localizeVendor($vendor, $ipAddress);
        }

        $this->vendorManager->create($vendor);

        return $vendor;
    }

    public function updateFromRequest(Vendor $vendor, VendorRequest $vendorRequest): void
    {
        $this->mapFromRequest($vendor, $vendorRequest);

        $this->vendorManager->update($vendor);
    }

    public function mapFromRequest(Vendor $vendor, VendorRequest $vendorRequest): void
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

    public function changePassword(Vendor $vendor, VendorPasswordChangeRequest $vendorPasswordChangeRequest): void
    {
        $this->vendorPasswordManager->changePassword(
            $vendor,
            $vendorPasswordChangeRequest->currentPassword,
            $vendorPasswordChangeRequest->newPassword
        );
    }

    public function startPasswordReset(VendorPasswordResetRequest $vendorPasswordResetRequest): void
    {
        $vendor = $this->vendorProvider->findOneByEmail($vendorPasswordResetRequest->emailAddress);

        if (! $vendor instanceof Vendor) {
            return;
        }

        $this->vendorPasswordManager->startPasswordReset($vendor);
    }

    public function concludePasswordReset(VendorPasswordResetTokenRequest $vendorPasswordResetTokenRequest): void
    {
        [$emailAddress, $token] = explode('|', base64_decode($vendorPasswordResetTokenRequest->token, true));

        $vendor = $this->vendorProvider->findOneByEmail($emailAddress);

        if (! $vendor instanceof Vendor) {
            throw new VendorNotFoundException();
        }

        $this->vendorPasswordManager->resetPassword($vendor, $token, $vendorPasswordResetTokenRequest->password);
    }

    public function updateSocialLink(Vendor $vendor, VendorSocialLinkRequest $vendorSocialLinkRequest): void
    {
        $vendor->setSocialLink($vendorSocialLinkRequest->network, $vendorSocialLinkRequest->link);

        $this->vendorManager->update($vendor);
    }

    private function localizeVendor(Vendor $vendor, string $ipAddress): void
    {
        try {
            if (null === $vendor->getCountry()) {
                $country = $this->geoIpReader->country($ipAddress);

                $vendor->setCountry($country->country->isoCode);
            }

            if (null === $vendor->getTimezone()) {
                $vendor->setTimezone(Timezones::forCountryCode($vendor->getCountry())[0]);
            }
        } catch (Exception) {
            // do nothing
        }
    }
}
