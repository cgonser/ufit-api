<?php

declare(strict_types=1);

namespace App\Vendor\ResponseMapper;

use App\Vendor\Dto\QuestionDto;
use App\Vendor\Entity\Question;

class QuestionResponseMapper
{
    public function map(Question $question): QuestionDto
    {
        $questionDto = new QuestionDto();
        $questionDto->id = $question->getId()
            ->toString();
        $questionDto->questionnaireId = $question->getQuestionnaire()
            ->getId()
            ->toString();
        $questionDto->question = $question->getQuestion() ?? '';
        $questionDto->order = $question->getOrder() ?? null;

        return $questionDto;
    }

    /**
     * @return QuestionDto[]
     */
    public function mapMultiple(array $questions): array
    {
        $questionDtos = [];

        foreach ($questions as $question) {
            $questionDtos[] = $this->map($question);
        }

        return $questionDtos;
    }
}
