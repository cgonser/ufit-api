<?php

declare(strict_types=1);

namespace App\Customer\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class CustomerPhotoRequest
{
    /**
     * @OA\Property()
     */
    public ?string $type = null;

    /**
     * @OA\Property()
     */
    public ?string $title = null;

    /**
     * @OA\Property()
     */
    public ?string $description = null;

    /**
     * @OA\Property()
     */
    public ?string $takenAt = null;
}
