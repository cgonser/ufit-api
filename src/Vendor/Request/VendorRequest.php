<?php

declare(strict_types=1);

namespace App\Vendor\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * @OA\RequestBody()
 */
class VendorRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     */
    public ?string $name = null;

    /**
     * @OA\Property()
     */
    public ?string $displayName = null;

    /**
     * @OA\Property()
     */
    public ?string $email = null;

    /**
     * @OA\Property()
     */
    public ?string $biography = null;

    /**
     * @OA\Property()
     */
    public ?string $password = null;

    /**
     * @OA\Property()
     */
    #[Regex(pattern: '/^(\w|\-)+$/', match: true)]
    public ?string $slug = null;

    /**
     * @OA\Property()
     */
    public ?string $country = null;

    /**
     * @OA\Property()
     */
    public ?string $locale = null;

    /**
     * @OA\Property()
     */
    public ?string $timezone = null;

    /**
     * @OA\Property()
     */
    public ?bool $allowEmailMarketing = null;

    /**
     * @OA\Property(type="array", @OA\Items(type="array", @OA\Items(type="string")))
     */
    public ?array $socialLinks = null;
}
