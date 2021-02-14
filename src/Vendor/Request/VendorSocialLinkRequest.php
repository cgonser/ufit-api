<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="VendorPhotoRequest"
 * )
 */
class VendorSocialLinkRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public string $network;

    /**
     * @OA\Property()
     */
    public ?string $link = null;
}