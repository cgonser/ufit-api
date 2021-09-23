<?php

declare(strict_types=1);

namespace App\Program\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class ProgramAssignmentRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     */
    public ?string $customerId;

    /**
     * @OA\Property()
     */
    public ?bool $isActive;

    /**
     * @OA\Property()
     */
    public ?string $expiresAt;
}
