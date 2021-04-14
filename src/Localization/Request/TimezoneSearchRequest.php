<?php

namespace App\Localization\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="timezoneSearchRequest"
 * )
 */
class TimezoneSearchRequest extends SearchRequest
{
    /**
     * @OA\Property()
     */
    public ?string $countryCode = null;
}
