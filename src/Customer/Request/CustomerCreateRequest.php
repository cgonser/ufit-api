<?php

namespace App\Customer\Request;

use App\Core\Request\AbstractRequest;
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
