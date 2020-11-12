<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="VendorUpdateRequest",
 *     required={"name", "email", "password"},
 * )
 */
class VendorUpdateRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $name = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public ?string $email = null;
}