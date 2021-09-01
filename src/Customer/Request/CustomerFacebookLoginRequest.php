<?php

declare(strict_types=1);

namespace App\Customer\Request;

use Symfony\Component\Validator\Constraints\NotBlank;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints;

/**
 * @OA\RequestBody(
 *     request="customerFacebookLoginRequest"
 * )
 */
class CustomerFacebookLoginRequest
{
    /**
     * @OA\Property()
     */
    #[NotBlank]
    public ?string $accessToken = null;

    /**
     * @OA\Property()
     */
    public ?string $facebookUserId = null;
}
