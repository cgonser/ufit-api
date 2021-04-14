<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="VendorPasswordResetRequest"
 * )
 */
class VendorPasswordResetRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $emailAddress = null;
}
