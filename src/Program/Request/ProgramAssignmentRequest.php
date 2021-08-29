<?php

declare(strict_types=1);

namespace App\Program\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="ProgramAssignmentRequest"
 * )
 */
class ProgramAssignmentRequest
{
    /**
     * @OA\Property()
     */
    public ?string $customerId = null;

    /**
     * @OA\Property()
     */
    public ?bool $isActive = null;

    /**
     * @OA\Property()
     */
    public ?string $expiresAt = null;
}
