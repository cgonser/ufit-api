<?php

namespace App\Customer\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="CustomerRequest"
 * )
 */
class CustomerRequest
{
    /**
     * @OA\Property()
     */
    public ?string $name = null;

    /**
     * @OA\Property()
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
     * @OA\Property(type="array", @OA\Items(type="object"))
     */
    public ?array $documents = null;

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
