<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorEmailAddressInUseException;
use App\Vendor\Exception\VendorInvalidPasswordException;
use App\Vendor\Exception\VendorSlugInUseException;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Repository\VendorRepository;
use App\Vendor\Request\VendorPasswordChangeRequest;
use App\Vendor\Request\VendorRequest;
use App\Vendor\Request\VendorSocialLinkRequest;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class VendorService
{
    private VendorRepository $vendorRepository;

    private VendorManager $vendorManager;

    private VendorProvider $vendorProvider;

    private VendorPhotoService $vendorPhotoService;

    private UserPasswordEncoderInterface $passwordEncoder;

    private SluggerInterface $slugger;

    public function __construct(
        VendorRepository $vendorRepository,
        VendorManager $vendorManager,
        VendorProvider $vendorProvider,
        VendorPhotoService $vendorPhotoService,
        UserPasswordEncoderInterface $passwordEncoder,
        SluggerInterface $slugger
    ) {
        $this->vendorRepository = $vendorRepository;
        $this->vendorManager = $vendorManager;
        $this->vendorProvider = $vendorProvider;
        $this->vendorPhotoService = $vendorPhotoService;
        $this->passwordEncoder = $passwordEncoder;
        $this->slugger = $slugger;
    }

    public function create(VendorRequest $vendorRequest): Vendor
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

    public function update(Vendor $vendor, VendorRequest $vendorRequest)
    {
        $this->mapFromRequest($vendor, $vendorRequest);

        $this->vendorRepository->save($vendor);

        if (null !== $vendorRequest->photoContents) {
            $this->vendorPhotoService->uploadPhoto(
                $vendor,
                $this->vendorPhotoService->decodePhotoContents($vendorRequest->photoContents)
            );
        }
    }

    public function mapFromRequest(Vendor $vendor, VendorRequest $vendorRequest)
    {
        if (null !== $vendorRequest->email) {
            $isEmailAddressInUse = $this->isEmailAddressInUse(
                $vendorRequest->email,
                $vendor->isNew() ? null : $vendor->getId()
            );

            if ($isEmailAddressInUse) {
                throw new VendorEmailAddressInUseException();
            }

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

        if (null !== $vendorRequest->password) {
            $vendor->setPassword($this->passwordEncoder->encodePassword($vendor, $vendorRequest->password));
        }

        if (null !== $vendorRequest->biography) {
            $vendor->setBiography($vendorRequest->biography);
        }

        if (null !== $vendorRequest->slug) {
            if (!$this->isSlugUnique($vendor, $vendorRequest->slug)) {
                throw new VendorSlugInUseException();
            }

            $vendor->setSlug($vendorRequest->slug);
        } elseif (null === $vendor->getSlug()) {
            $vendor->setSlug($this->generateSlug($vendor));
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

        if ($vendor->isNew() || 0 == count($vendor->getRoles())) {
            $vendor->setRoles(['ROLE_VENDOR']);
        }
    }

    public function isEmailAddressInUse(string $emailAddress, ?UuidInterface $vendorId = null): bool
    {
        /** @var Vendor $existingVendor */
        $existingVendor = $this->vendorProvider->findOneByEmail($emailAddress);

        if (null === $existingVendor) {
            return false;
        }

        if (null !== $vendorId && $existingVendor->getId()->equals($vendorId)) {
            return false;
        }

        return true;
    }

    public function changePassword(
        Vendor $vendor,
        VendorPasswordChangeRequest $vendorPasswordChangeRequest
    ) {
        $isPasswordValid = $this->passwordEncoder->isPasswordValid(
            $vendor,
            $vendorPasswordChangeRequest->currentPassword
        );

        if (!$isPasswordValid) {
            throw new VendorInvalidPasswordException();
        }

        $vendor->setPassword(
            $this->passwordEncoder->encodePassword($vendor, $vendorPasswordChangeRequest->newPassword)
        );

        $this->vendorRepository->save($vendor);
    }

    public function generateSlug(Vendor $vendor, ?int $suffix = null): string
    {
        $slug = strtolower($this->slugger->slug($vendor->getName()));

        if (null !== $suffix) {
            $slug .= '-'.(string) $suffix;
        }

        if ($this->isSlugUnique($vendor, $slug)) {
            return $slug;
        }

        $suffix = null !== $suffix ? $suffix + 1 : 1;

        return $this->generateSlug($vendor, $suffix);
    }

    private function isSlugUnique(Vendor $vendor, string $slug): bool
    {
        $existingVendor = $this->vendorProvider->findOneBySlug($slug);

        if (!$existingVendor) {
            return true;
        }

        if (!$vendor->isNew() && $existingVendor->getId()->toString() == $vendor->getId()->toString()) {
            return true;
        }

        return false;
    }

    public function updateName(Vendor $vendor, string $name)
    {
        $vendor->setName($name);

        $this->vendorRepository->save($vendor);
    }

    public function updateSocialLink(Vendor $vendor, VendorSocialLinkRequest $vendorSocialLinkRequest)
    {
        $vendor->setSocialLink($vendorSocialLinkRequest->network, $vendorSocialLinkRequest->link);

        $this->vendorRepository->save($vendor);
    }
}
