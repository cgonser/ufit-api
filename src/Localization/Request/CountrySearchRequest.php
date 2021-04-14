<?php

namespace App\Localization\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="countrySearchRequest"
 * )
 */
class CountrySearchRequest extends SearchRequest
{
    /**
     * @OA\Property()
     */
    public ?string $orderProperty = 'code';

    /**
     * @OA\Property()
     */
    public ?string $orderDirection = 'ASC';

    /**
     * @OA\Property()
     */
    public ?string $code;
}
