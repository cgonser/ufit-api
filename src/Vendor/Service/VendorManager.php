<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Core\Validation\EntityValidator;
use App\Vendor\Entity\Vendor;
use App\Vendor\Exception\VendorSlugInUseException;
use App\Vendor\Message\VendorCreatedEvent;
use App\Vendor\Message\VendorUpdatedEvent;
use App\Vendor\Repository\VendorRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class VendorManager
{
    public function __construct(
        private VendorRepository $vendorRepository,
        private SluggerInterface $slugger,
        private EntityValidator $entityValidator,
        private MessageBusInterface $messageBus
    ) {
    }

    public function create(Vendor $vendor): void
    {
        if ([] === $vendor->getRoles()) {
            $vendor->setRoles(['ROLE_VENDOR']);
        }

        $this->save($vendor);

        $this->messageBus->dispatch(new VendorCreatedEvent($vendor->getId()));
    }

    public function update(Vendor $vendor): void
    {
        $this->save($vendor);

        $this->messageBus->dispatch(new VendorUpdatedEvent($vendor->getId()));
    }

    public function save(Vendor $vendor): void
    {
        $this->validateVendor($vendor);

        $this->vendorRepository->save($vendor);
    }

    public function delete(Vendor $vendor): void
    {
        $this->vendorRepository->delete($vendor);
    }

    public function generateSlug(Vendor $vendor, ?int $suffix = null): string
    {
        $slug = strtolower($this->slugger->slug($vendor->getDisplayName())->toString());

        if (null !== $suffix) {
            $slug .= '-'.$suffix;
        }

        if ($this->isSlugUnique($vendor, $slug)) {
            return $slug;
        }

        $suffix = null !== $suffix ? $suffix + 1 : 1;

        return $this->generateSlug($vendor, $suffix);
    }

    private function validateVendor(Vendor $vendor): void
    {
        $this->entityValidator->validate($vendor);

        if (null !== $vendor->getSlug() && ! $this->isSlugUnique($vendor, $vendor->getSlug())) {
            throw new VendorSlugInUseException();
        }
    }

    private function isSlugUnique(Vendor $vendor, string $slug): bool
    {
        $existingVendor = $this->vendorRepository->findOneBy([
            'slug' => $slug,
        ]);

        if (! $existingVendor) {
            return true;
        }

        return !$vendor->isNew() && $existingVendor->getId()->equals($vendor->getId());
    }
}
