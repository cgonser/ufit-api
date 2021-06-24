<?php

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody()
 */
class CustomerEmailChangeRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $email = null;
}