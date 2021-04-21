<?php

namespace App\Vendor\Service;

use App\Core\Validation\EntityValidator;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorSlugInUseException;
use App\Vendor\Repository\VendorRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class VendorManager
{
    private VendorRepository $vendorRepository;

    private SluggerInterface $slugger;

    private EntityValidator $validator;

    public function __construct(
        VendorRepository $vendorRepository,
        SluggerInterface $slugger,
        EntityValidator $validator
    ) {
        $this->vendorRepository = $vendorRepository;
        $this->slugger = $slugger;
        $this->validator = $validator;
    }

    public function create(Vendor $vendor): void
    {
        if (0 === count($vendor->getRoles())) {
            $vendor->setRoles(['ROLE_VENDOR']);
        }

        $this->validateVendor($vendor);

        $this->vendorRepository->save($vendor);
    }

    public function update(Vendor $vendor): void
    {
        $this->validateVendor($vendor);

        $this->vendorRepository->save($vendor);
    }

    public function delete(Vendor $vendor): void
    {
        $this->vendorRepository->delete($vendor);
    }

    private function validateVendor(Vendor $vendor)
    {
        $this->validator->validate($vendor);

        if (null !== $vendor->getSlug() && !$this->isSlugUnique($vendor, $vendor->getSlug())) {
            throw new VendorSlugInUseException();
        }
    }

    public function generateSlug(Vendor $vendor, ?int $suffix = null): string
    {
        $slug = strtolower($this->slugger->slug($vendor->getName()));

        if (null !== $suffix) {
            $slug .= '-'.$suffix;
        }

        if ($this->isSlugUnique($vendor, $slug)) {
            return $slug;
        }

        $suffix = null !== $suffix ? $suffix + 1 : 1;

        return $this->generateSlug($vendor, $suffix);
    }

    private function isSlugUnique(Vendor $vendor, string $slug): bool
    {
        $existingVendor = $this->vendorRepository->findOneBy(['slug' => $slug]);

        if (!$existingVendor) {
            return true;
        }

        if (!$vendor->isNew() && $existingVendor->getId()->equals($vendor->getId())) {
            return true;
        }

        return false;
    }
}
