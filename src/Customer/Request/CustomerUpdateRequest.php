<?php

namespace App\Customer\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CustomerUpdateRequest
{
    /**
     * @Assert\NotBlank()
     */
    public ?string $name = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public ?string $email = null;
}