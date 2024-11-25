<?php

declare(strict_types=1);

namespace App\Localization\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="CountryRequest"
 * )
 */
class CountryRequest
{
    /**
     * @OA\Property()
     */
    public ?string $name = null;

    /**
     * @OA\Property()
     */
    public ?string $code = null;

    /**
     * @OA\Property()
     */
    public ?string $currencyId = null;

    /**
     * @OA\Property()
     */
    public ?string $primaryTimezone = null;

    /**
     * @OA\Property()
     */
    public ?string $primaryLocale = null;

    /**
     * @OA\Property()
     */
    public ?bool $vendorsEnabled = null;

    /**
     * @OA\Property()
     */
    public ?bool $customersEnabled = null;

    /**
     * @OA\Property()
     */
    public ?string $documentName = null;
}
