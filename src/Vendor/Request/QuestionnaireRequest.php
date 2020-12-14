<?php

namespace App\Vendor\Request;

use Nelmio\ApiDocBundle\Annotation\Model;
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

    /**
     * @var QuestionRequest[]
     *
     * @OA\Property(
     *     type="array",
     *     @OA\Items(ref=@Model(type=QuestionRequest::class)))
     * )
     */
    public array $questions = [];
}
