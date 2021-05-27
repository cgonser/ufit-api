<?php

namespace App\Vendor\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class VendorSettingRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     */
    public ?string $vendorId;

    /**
     * @OA\Property()
     */
    public ?string $name;

    /**
     * @OA\Property()
     */
    public ?string $value;
}