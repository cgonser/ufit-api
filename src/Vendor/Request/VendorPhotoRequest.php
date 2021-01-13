<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="VendorPhotoRequest"
 * )
 */
class VendorPhotoRequest
{
    /**
     * @OA\Property()
     */
    public ?string $photoContents = null;
}