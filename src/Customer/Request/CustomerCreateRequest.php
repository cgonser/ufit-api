<?php

declare(strict_types=1);

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody()
 */
class CustomerCreateRequest extends CustomerRequest
{
    /**
     * @OA\Property()
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public ?string $email = null;
}
