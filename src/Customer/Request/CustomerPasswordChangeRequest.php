<?php

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="CustomerPasswordChangeRequest"
 * )
 */
class CustomerPasswordChangeRequest
{
    /**
     * @OA\Property()
     */
    public ?string $currentPassword = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $newPassword = null;
}