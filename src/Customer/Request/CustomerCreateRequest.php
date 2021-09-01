<?php

declare(strict_types=1);

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints;

/**
 * @OA\RequestBody()
 */
class CustomerCreateRequest extends CustomerRequest
{
    /**
     * @OA\Property()
     */
    #[Constraints\NotNull]
    #[Constraints\NotBlank]
    #[Constraints\Email]
    public ?string $email = null;
}
