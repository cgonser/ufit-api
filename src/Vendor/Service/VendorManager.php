<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Vendor;
use App\Vendor\Repository\VendorRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class VendorManager
{
    private VendorRepository $vendorRepository;

    private UserPasswordEncoderInterface $passwordEncoder;

    private SluggerInterface $slugger;

    public function __construct(
        VendorRepository $vendorRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        SluggerInterface $slugger
    )
    {
        $this->vendorRepository = $vendorRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->slugger = $slugger;
    }

    public function createVendor(Vendor $vendor)
    {
        $vendor->setPassword(
            $this->passwordEncoder->encodePassword($vendor, $vendor->getPassword())
        );
        $vendor->setRoles(['ROLE_VENDOR']);
        $vendor->setSlug($this->slugger->slug($vendor->getName()));

        $this->vendorRepository->save($vendor);
    }
}