<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="QuestionnaireUpdateRequest",
 *     required={"name"}
 * )
 */
class QuestionnaireUpdateRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $title = null;
}