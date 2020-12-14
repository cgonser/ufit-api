<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="QuestionRequest",
 *     required={"order", "question"}
 * )
 */
class QuestionRequest
{
    /**
     * @OA\Property()
     */
    public ?string $id = null;

    /**
     * @OA\Property()
     * @Assert\Positive()
     */
    public ?int $order = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $question = null;
}
