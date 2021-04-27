<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="VendorPasswordChangeRequest"
 * )
 */
class VendorPasswordChangeRequest
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