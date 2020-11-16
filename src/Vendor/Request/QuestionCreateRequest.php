<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="QuestionCreateRequest",
 *     required={"order", "question"}
 * )
 */
class QuestionCreateRequest
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
