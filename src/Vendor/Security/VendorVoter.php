<?php

declare(strict_types=1);

namespace App\Vendor\Security;

use App\Vendor\Entity\Vendor;

class VendorVoter extends AbstractVendorAuthorizationVoter
{
    public function isSubjectSupported($subject): bool
    {
        return $subject instanceof Vendor || Vendor::class === $subject;
    }

    protected function vendorCanModifyEntity(object $subject, Vendor $vendor): bool
    {
        return $vendor === $subject;
    }

    protected function canRead($subject, Vendor $vendor): bool
    {
        return $vendor === $subject;
    }
}
