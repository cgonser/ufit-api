<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\RequestBody(
 *     request="QuestionnaireSearchRequest"
 * )
 */
class QuestionnaireSearchRequest
{
    /**
     * @OA\Property()
     */
    public ?string $vendorId = null;

    /**
     * @OA\Property()
     */
    public ?string $title = null;
}