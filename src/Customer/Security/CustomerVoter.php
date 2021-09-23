<?php

declare(strict_types=1);

namespace App\Customer\Security;

use App\Customer\Entity\Customer;

class CustomerVoter extends AbstractCustomerAuthorizationVoter
{
    public function isSubjectSupported($subject): bool
    {
        return $subject instanceof Customer || Customer::class === $subject;
    }

    protected function customerCanModifyEntity(object $subject, Customer $customer): bool
    {
        return $customer === $subject;
    }

    protected function canRead($subject, Customer $customer): bool
    {
        return $customer === $subject;
    }
}
