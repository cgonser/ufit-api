<?php

declare(strict_types=1);

namespace App\Program\Request;

use App\Core\Request\SearchRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="ProgramAssignmentSearchRequest"
 * )
 */
class ProgramAssignmentSearchRequest extends SearchRequest
{
    /**
     * @OA\Property()
     */
    public ?string $customerId = null;

    /**
     * @OA\Property()
     */
    public ?string $vendorId = null;

    /**
     * @OA\Property()
     */
    public ?string $programId = null;
}
