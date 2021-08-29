<?php

declare(strict_types=1);

namespace App\Program\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class ProgramAssetRequest
{
    /**
     * @OA\Property()
     */
    public string $filename;

    /**
     * @OA\Property()
     */
    public ?string $title = null;

    /**
     * @OA\Property()
     */
    public ?string $type = null;
}
