<?php

namespace App\Vendor\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class VendorRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     */
    public ?string $name;

    /**
     * @OA\Property()
     */
    public ?string $displayName;

    /**
     * @OA\Property()
     */
    public ?string $email;

    /**
     * @OA\Property()
     */
    public ?string $biography;

    /**
     * @OA\Property()
     */
    public ?string $password;

    /**
     * @OA\Property()
     */
    public ?string $slug;

    /**
     * @OA\Property()
     */
    public ?string $country;

    /**
     * @OA\Property()
     */
    public ?string $locale;

    /**
     * @OA\Property()
     */
    public ?string $timezone;

    /**
     * @OA\Property()
     */
    public ?string $photoContents;

    /**
     * @OA\Property()
     */
    public ?bool $allowEmailMarketing;

    /**
     * @OA\Property(type="array", @OA\Items(type="array", @OA\Items(type="string")))
     */
    public ?array $socialLinks;
}
