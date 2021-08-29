<?php

declare(strict_types=1);

namespace App\Vendor\Dto;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class QuestionnaireDto
{
    public string $id;

    public string $vendorId;

    public string $title;

    public string $createdAt;

    /**
     * @var QuestionDto[]
     * @OA\Property(type="array", @OA\Items(ref=@Model(type=QuestionDto::class)))
     */
    public array $questions = [];
}
