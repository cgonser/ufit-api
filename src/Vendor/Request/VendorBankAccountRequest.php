<?php

namespace App\Vendor\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class VendorBankAccountRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     */
    public ?string $vendorId;

    /**
     * @OA\Property()
     */
    public ?string $description;

    /**
     * @OA\Property()
     */
    public ?string $bankCode;

    /**
     * @OA\Property()
     */
    public ?string $agencyNumber;

    /**
     * @OA\Property()
     */
    public ?string $accountNumber;

    /**
     * @OA\Property()
     */
    public ?string $accountDigit;

    /**
     * @OA\Property()
     */
    public ?string $ownerName;

    /**
     * @OA\Property()
     */
    public ?string $ownerDocumentType;

    /**
     * @OA\Property()
     */
    public ?string $ownerDocumentNumber;
}