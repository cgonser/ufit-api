<?php

declare(strict_types=1);

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
    public ?string $vendorId = null;

    /**
     * @OA\Property()
     */
    public ?string $description = null;

    /**
     * @OA\Property()
     */
    public ?string $bankCode = null;

    /**
     * @OA\Property()
     */
    public ?string $agencyNumber = null;

    /**
     * @OA\Property()
     */
    public ?string $accountNumber = null;

    /**
     * @OA\Property()
     */
    public ?string $accountDigit = null;

    /**
     * @OA\Property()
     */
    public ?string $ownerName = null;

    /**
     * @OA\Property()
     */
    public ?string $ownerDocumentType = null;

    /**
     * @OA\Property()
     */
    public ?string $ownerDocumentNumber = null;
}
