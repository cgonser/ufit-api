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

class VendorRequestManager
{
    private VendorManager $vendorManager;

    private VendorProvider $vendorProvider;

    private VendorPhotoService $vendorPhotoService;

    private VendorPasswordManager $vendorPasswordManager;

    public function __construct(
        VendorManager $vendorManager,
        VendorProvider $vendorProvider,
        VendorPhotoService $vendorPhotoService,
        VendorPasswordManager $vendorPasswordManager
    ) {
        $this->vendorManager = $vendorManager;
        $this->vendorProvider = $vendorProvider;
        $this->vendorPhotoService = $vendorPhotoService;
        $this->vendorPasswordManager = $vendorPasswordManager;
    }

    public function createFromRequest(VendorRequest $vendorRequest): Vendor
    {
        $vendor = new Vendor();

        $this->mapFromRequest($vendor, $vendorRequest);

        $this->vendorManager->create($vendor);

        if (null !== $vendorRequest->photoContents) {
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
        if (null !== $vendorRequest->email) {
            $vendor->setEmail($vendorRequest->email);
        }

        if (null !== $vendorRequest->name) {
            $vendor->setName($vendorRequest->name);
        }

        if (null !== $vendorRequest->displayName) {
            $vendor->setDisplayName($vendorRequest->displayName);
        }

        if (null === $vendor->getDisplayName()) {
            $vendor->setDisplayName($vendor->getName());
        }

        if (null !== $vendorRequest->password && null === $vendor->getPassword()) {
            $vendor->setPassword($this->vendorPasswordManager->encodePassword($vendor, $vendorRequest->password));
        }

        if (null !== $vendorRequest->biography) {
            $vendor->setBiography($vendorRequest->biography);
        }

        if (null !== $vendorRequest->slug) {
            $vendor->setSlug($vendorRequest->slug);
        } elseif (null === $vendor->getSlug() && null !== $vendor->getName()) {
            $vendor->setSlug($this->vendorManager->generateSlug($vendor));
        }

        if (null !== $vendorRequest->allowEmailMarketing) {
            $vendor->setAllowEmailMarketing($vendorRequest->allowEmailMarketing);
        }

        if (null !== $vendorRequest->socialLinks) {
            $vendor->setSocialLinks($vendorRequest->socialLinks);
        }

        if (null !== $vendorRequest->country) {
            $vendor->setCountry($vendorRequest->country);
        }

        if (null !== $vendorRequest->locale) {
            $vendor->setLocale($vendorRequest->locale);
        }

        if (null !== $vendorRequest->timezone) {
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
}
