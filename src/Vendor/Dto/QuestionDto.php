<?php

declare(strict_types=1);

namespace App\Vendor\Dto;

class QuestionDto
{
    public string $id;

    public string $questionnaireId;

    public string $question;

    public ?int $order = null;
}
