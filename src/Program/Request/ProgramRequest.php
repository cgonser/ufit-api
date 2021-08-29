<?php

declare(strict_types=1);

namespace App\Program\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="ProgramRequest"
 * )
 */
class ProgramRequest
{
    /**
     * @OA\Property()
     */
    public ?string $vendorId = null;

    /**
     * @OA\Property()
     */
    public ?string $name = null;

    /**
     * @OA\Property()
     */
    public ?string $level = null;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $goals = null;

    /**
     * @OA\Property()
     */
    public ?string $description = null;

    /**
     * @OA\Property()
     */
    public ?bool $isTemplate = null;

    /**
     * @OA\Property()
     */
    public ?bool $isActive = null;
}
