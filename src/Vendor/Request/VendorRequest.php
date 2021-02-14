<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="VendorRequest",
 *     required={"name", "email"},
 * )
 */
class VendorRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $name = null;

    /**
     * @OA\Property()
     */
    public ?string $displayName = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     * @Assert\Email()
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
    public ?string $photoContents = null;

    /**
     * @OA\Property()
     */
    public ?bool $allowEmailMarketing = null;

    /**
     * @OA\Property(type="array", @OA\Items(type="array", @OA\Items(type="string")))
     */
    public ?array $socialLinks = null;
}
