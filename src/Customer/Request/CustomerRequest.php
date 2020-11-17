<?php

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="CustomerRequest",
 *     required={"name", "email"},
 * )
 */
class CustomerRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $name = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public ?string $email = null;

    /**
     * @OA\Property()
     */
    public ?string $password = null;
}