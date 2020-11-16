<?php

namespace App\Vendor\Request;

use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\RequestBody(
 *     request="QuestionnaireCreateRequest",
 *     required={"vendorId", "name"}
 * )
 */
class QuestionnaireCreateRequest
{
    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $vendorId = null;

    /**
     * @OA\Property()
     * @Assert\NotBlank()
     */
    public ?string $title = null;
}