<?php

declare(strict_types=1);

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @OA\RequestBody()
 */
class VendorLoginFacebookRequest
{
    /**
     * @OA\Property()
     */
    #[NotBlank]
    public ?string $accessToken = null;
}
