<?php

namespace App\Vendor\Dto;

use OpenApi\Annotations as OA;

class QuestionnaireDto
{
    public string $id;

    public string $vendorId;

    public string $title;

    public string $createdAt;

    /**
     * @OA\Property(type="array", @OA\Items(type="QuestionDto"))
     */
    public array $questions = [];
}
