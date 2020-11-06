<?php

namespace App\Customer\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="CustomerCreateRequest",
 *     required={"name", "email", "password"},
 * )
 */
class CustomerCreateRequest
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
     * @Assert\NotBlank()
     */
    public ?string $password = null;
}