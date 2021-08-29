<?php

declare(strict_types=1);

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @OA\RequestBody(
 *     request="VendorPhotoRequest"
 * )
 */
class VendorSocialLinkRequest
{
    /**
     * @OA\Property()
     */
    #[NotBlank]
    public string $network;

    /**
     * @OA\Property()
     */
    public ?string $link = null;
}
