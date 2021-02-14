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
    public ?string $phone = null;

    /**
     * @OA\Property()
     */
    public ?string $password = null;

    /**
     * @OA\Property()
     */
    public ?int $height = null;

    /**
     * @OA\Property()
     */
    public ?string $birthDate = null;

    /**
     * @OA\Property()
     */
    public ?string $gender = null;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $goals = null;

    /**
     * @OA\Property()
     */
    public ?string $country = null;

    /**
     * @OA\Property()
     */
    public ?string $locale = null;

    /**
     * @OA\Property()
     */
    public ?string $timezone = null;
}