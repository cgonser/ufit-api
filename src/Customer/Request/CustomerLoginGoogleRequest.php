<?php

declare(strict_types=1);

namespace App\Customer\Request;

use Symfony\Component\Validator\Constraints\NotBlank;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints;

/**
 * @OA\RequestBody(
 *     request="CustomerLoginGoogleRequest",
 *     required={"accessToken", "userId"},
 * )
 */
class CustomerLoginGoogleRequest
{
    /**
     * @OA\Property()
     */
    #[NotBlank]
    public ?string $accessToken = null;

    /**
     * @OA\Property()
     */
    #[NotBlank]
    public ?string $userId = null;
}
