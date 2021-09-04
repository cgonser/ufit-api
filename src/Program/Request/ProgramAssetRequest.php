<?php

declare(strict_types=1);

namespace App\Program\Request;

use App\Core\Request\AbstractRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody()
 */
class ProgramAssetRequest extends AbstractRequest
{
    /**
     * @OA\Property()
     */
    public string $filename;

    /**
     * @OA\Property()
     */
    public ?string $title;

    /**
     * @OA\Property()
     */
    public ?string $type;
}
