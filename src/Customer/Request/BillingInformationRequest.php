<?php

namespace App\Customer\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class BillingInformationRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     */
    public ?string $customerId = null;

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
    public ?string $birthDate = null;

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
    public ?string $documentType = null;

    /**
     * @OA\Property()
     */
    public ?string $documentNumber = null;

    /**
     * @OA\Property()
     */
    public ?string $addressLine1 = null;

    /**
     * @OA\Property()
     */
    public ?string $addressLine2 = null;

    /**
     * @OA\Property()
     */
    public ?string $addressNumber = null;

    /**
     * @OA\Property()
     */
    public ?string $addressDistrict = null;

    /**
     * @OA\Property()
     */
    public ?string $addressCity = null;

    /**
     * @OA\Property()
     */
    public ?string $addressState = null;

    /**
     * @OA\Property()
     */
    public ?string $addressCountry = null;

    /**
     * @OA\Property()
     */
    public ?string $addressZipCode = null;
}
