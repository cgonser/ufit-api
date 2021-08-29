<?php

declare(strict_types=1);

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="CustomerPasswordResetTokenRequest"
 * )
 */
class CustomerPasswordResetTokenRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $token = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $password = null;
}
