<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorEmailAddressInUseException;
use App\Vendor\Exception\VendorInvalidPasswordException;
use App\Vendor\Exception\VendorSlugInUseException;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Repository\VendorRepository;
use App\Vendor\Request\VendorCreateRequest;
use App\Vendor\Request\VendorPasswordChangeRequest;
use App\Vendor\Request\VendorUpdateRequest;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class VendorService
{
    private VendorRepository $vendorRepository;

    private VendorProvider $vendorProvider;

    private UserPasswordEncoderInterface $passwordEncoder;

    private SluggerInterface $slugger;

    public function __construct(
        VendorRepository $vendorRepository,
        VendorProvider $vendorProvider,
        UserPasswordEncoderInterface $passwordEncoder,
        SluggerInterface $slugger
    ) {
        $this->vendorRepository = $vendorRepository;
        $this->vendorProvider = $vendorProvider;
        $this->passwordEncoder = $passwordEncoder;
        $this->slugger = $slugger;
    }

    public function create(VendorCreateRequest $vendorCreateRequest): Vendor
    {
        if ($this->isEmailAddressInUse($vendorCreateRequest->email)) {
            throw new VendorEmailAddressInUseException();
        }

        $vendor = new Vendor();
        $vendor->setName($vendorCreateRequest->name);
        $vendor->setEmail($vendorCreateRequest->email);
        $vendor->setPassword($this->passwordEncoder->encodePassword($vendor, $vendorCreateRequest->password));
        $vendor->setSlug($this->generateSlug($vendor));
        $vendor->setRoles(['ROLE_VENDOR']);

        $this->vendorRepository->save($vendor);

        return $vendor;
    }

    public function update(Vendor $vendor, VendorUpdateRequest $vendorUpdateRequest)
    {
        if ($this->isEmailAddressInUse($vendorUpdateRequest->email, $vendor->getId())) {
            throw new VendorEmailAddressInUseException();
        }

        if (!$this->isSlugUnique($vendor, $vendorUpdateRequest->slug)) {
            throw new VendorSlugInUseException();
        }

        $vendor->setName($vendorUpdateRequest->name);
        $vendor->setEmail($vendorUpdateRequest->email);
        $vendor->setSlug($vendorUpdateRequest->slug);

        $this->vendorRepository->save($vendor);
    }

    public function isEmailAddressInUse(string $emailAddress, ?UuidInterface $vendorId = null): bool
    {
        /** @var Vendor $existingVendor */
        $existingVendor = $this->vendorProvider->findOneByEmail($emailAddress);

        if (null === $existingVendor) {
            return false;
        }

        if (null !== $vendorId && $existingVendor->getId()->toString() == $vendorId->toString()) {
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
}
