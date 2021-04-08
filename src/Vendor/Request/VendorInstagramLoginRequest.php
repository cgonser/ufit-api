<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="VendorInstagramLoginRequest",
 *     required={"code", "email"},
 * )
 */
class VendorInstagramLoginRequest
{
    /**
     * @OA\Property()
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public ?string $code = null;

    /**
     * @OA\Property()
     * @Assert\Email()
     */
    public ?string $email = null;
}