<?php

declare(strict_types=1);

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints;

/**
 * @OA\RequestBody()
 */
class CustomerPasswordChangeRequest
{
    /**
     * @OA\Property()
     */
    public ?string $currentPassword = null;

    /**
     * @OA\Property()
     */
    #[Constraints\NotBlank]
    public ?string $newPassword = null;
}
