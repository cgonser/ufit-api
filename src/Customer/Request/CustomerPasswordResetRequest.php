<?php

declare(strict_types=1);

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints;

/**
 * @OA\RequestBody()
 */
class CustomerPasswordResetRequest
{
    /**
     * @OA\Property()
     */
    #[Constraints\NotBlank]
    public ?string $emailAddress = null;
}
