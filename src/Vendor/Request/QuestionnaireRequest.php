<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="QuestionnaireRequest",
 *     required={"name"}
 * )
 */
class QuestionnaireRequest
{
    /**
     * @OA\Property()
     */
    public ?string $vendorId = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $title = null;
}