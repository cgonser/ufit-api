<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="VendorPasswordResetTokenRequest"
 * )
 */
class VendorPasswordResetTokenRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $token = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $password = null;
}
