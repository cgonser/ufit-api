<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="QuestionUpdateRequest",
 *     required={"order", "question"}
 * )
 */
class QuestionUpdateRequest
{
    /**
     * @OA\Property()
     * @Assert\Positive()
     */
    public ?string $order = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $question = null;
}
