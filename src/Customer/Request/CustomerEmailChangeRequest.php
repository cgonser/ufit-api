<?php

declare(strict_types=1);

namespace App\Customer\Request;

use Symfony\Component\Validator\Constraints\NotBlank;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints;

/**
 * @OA\RequestBody()
 */
class CustomerEmailChangeRequest
{
    /**
     * @OA\Property()
     */
    #[NotBlank]
    public ?string $email = null;
}
