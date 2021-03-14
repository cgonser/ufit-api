<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="VendorSettingRequest"
 * )
 */
class VendorSettingRequest
{
    /**
     * @OA\Property()
     */
    public ?string $vendorId = null;

    /**
     * @OA\Property()
     */
    public ?string $name = null;

    /**
     * @OA\Property()
     */
    public ?string $value = null;
}