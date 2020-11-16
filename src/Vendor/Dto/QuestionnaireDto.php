<?php

namespace App\Vendor\Dto;

class QuestionnaireDto
{
    public string $id;

    public string $vendorId;

    public string $title;

    public string $createdAt;

    public array $questions = [];
}
