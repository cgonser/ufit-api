<?php

namespace App\Customer\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CustomerPasswordChangeRequest
{
    /**
     * @Assert\NotBlank()
     */
    public ?string $currentPassword = null;

    /**
     * @Assert\NotBlank()
     */
    public ?string $newPassword = null;
}