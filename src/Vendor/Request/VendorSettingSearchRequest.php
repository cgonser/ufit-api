<?php

declare(strict_types=1);

namespace App\Vendor\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class VendorSettingSearchRequest extends SearchRequest
{
    /**
     * @OA\Property()
     */
    public ?string $vendorId = null;
}
