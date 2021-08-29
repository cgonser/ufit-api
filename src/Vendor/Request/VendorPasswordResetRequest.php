<?php

declare(strict_types=1);

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @OA\RequestBody(
 *     request="VendorPasswordResetRequest"
 * )
 */
class VendorPasswordResetRequest
{
    /**
     * @OA\Property()
     */
    #[NotBlank]
    public ?string $emailAddress = null;
}
