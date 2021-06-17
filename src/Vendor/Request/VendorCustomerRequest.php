<?php

namespace App\Vendor\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class VendorCustomerRequest extends AbstractRequest
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
    public ?string $phoneIntlCode = null;

    /**
     * @OA\Property()
     */
    public ?string $phoneAreaCode = null;

    /**
     * @OA\Property()
     */
    public ?string $phoneNumber = null;

    /**
     * @OA\Property()
     */
    public ?int $height = null;

    /**
     * @OA\Property()
     */
    public ?int $lastWeight = null;

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
}
