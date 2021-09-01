<?php

declare(strict_types=1);

namespace App\Program\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class ProgramRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     */
    public ?string $vendorId;

    /**
     * @OA\Property()
     */
    public ?string $name;

    /**
     * @OA\Property()
     */
    public ?string $level;

    /**
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    public ?array $goals;

    /**
     * @OA\Property()
     */
    public ?string $description;

    /**
     * @OA\Property()
     */
    public ?bool $isTemplate;

    /**
     * @OA\Property()
     */
    public ?bool $isActive;
}
