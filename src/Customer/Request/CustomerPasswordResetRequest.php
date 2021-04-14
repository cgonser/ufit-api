<?php

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="CustomerPasswordResetRequest"
 * )
 */
class CustomerPasswordResetRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $emailAddress = null;
}
