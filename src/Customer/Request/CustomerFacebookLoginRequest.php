<?php

declare(strict_types=1);

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="customerFacebookLoginRequest"
 * )
 */
class CustomerFacebookLoginRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $accessToken = null;

    /**
     * @OA\Property()
     */
    public ?string $facebookUserId = null;
}
