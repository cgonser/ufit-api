<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="VendorInstagramLoginRequest",
 *     required={"code"},
 * )
 */
class VendorInstagramLoginRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $code = null;
}