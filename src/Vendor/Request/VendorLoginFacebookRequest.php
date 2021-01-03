<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="VendorLoginFacebookRequest",
 *     required={"accessToken", "userId"},
 * )
 */
class VendorLoginFacebookRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $accessToken = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $userId = null;
}