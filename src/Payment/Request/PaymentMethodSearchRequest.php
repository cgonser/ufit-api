<?php

declare(strict_types=1);

namespace App\Payment\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class PaymentMethodSearchRequest extends SearchRequest
{
    /**
     * @OA\Property()
     */
    public ?string $orderProperty = 'name';

    /**
     * @OA\Property()
     */
    public ?string $orderDirection = 'ASC';

    /**
     * @OA\Property()
     */
    public ?string $countryCode = null;
}
