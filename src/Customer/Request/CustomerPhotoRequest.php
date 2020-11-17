<?php

namespace App\Customer\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="CustomerPhotoRequest"
 * )
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
    public ?string $file = null;

    /**
     * @OA\Property()
     */
    public ?string $takenAt = null;
}
