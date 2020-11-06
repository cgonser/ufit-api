<?php

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="CustomerPasswordChangeRequest",
 *     required={"name", "email", "password"},
 * )
 */
class CustomerPasswordChangeRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $currentPassword = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $newPassword = null;
}