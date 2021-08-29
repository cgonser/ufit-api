<?php

declare(strict_types=1);

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @OA\RequestBody(
 *     request="VendorPasswordResetTokenRequest"
 * )
 */
class VendorPasswordResetTokenRequest
{
    /**
     * @OA\Property()
     */
    #[NotBlank]
    public ?string $token = null;

    /**
     * @OA\Property()
     */
    #[NotBlank]
    public ?string $password = null;
}
