<?php

declare(strict_types=1);

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

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
     */
    #[NotNull]
    #[NotBlank]
    public ?string $code = null;

    /**
     * @OA\Property()
     */
    #[Email]
    public ?string $email = null;
}
