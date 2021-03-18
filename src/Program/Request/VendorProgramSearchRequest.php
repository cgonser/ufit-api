<?php

namespace App\Program\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="VendorProgramSearchRequest"
 * )
 */
class VendorProgramSearchRequest extends SearchRequest
{
    /**
     * @OA\Property()
     */
    public ?string $vendorId = null;
}