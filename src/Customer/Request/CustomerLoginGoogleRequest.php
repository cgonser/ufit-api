<?php

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank()
     */
    public ?string $accessToken = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $userId = null;
}