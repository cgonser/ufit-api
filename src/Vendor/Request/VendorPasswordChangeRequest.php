<?php

declare(strict_types=1);

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints\NotBlank;

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
     */
    #[NotBlank]
    public ?string $newPassword = null;
}
