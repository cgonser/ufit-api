<?php

declare(strict_types=1);

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

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
     */
    #[Positive]
    public ?int $order = null;

    /**
     * @OA\Property()
     */
    #[NotBlank]
    public ?string $question = null;
}
