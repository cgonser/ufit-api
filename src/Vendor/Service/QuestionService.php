<?php

declare(strict_types=1);

namespace App\Vendor\Service;

use App\Vendor\Entity\Question;
use App\Vendor\Entity\Questionnaire;
use App\Vendor\Repository\QuestionnaireRepository;
use App\Vendor\Repository\QuestionRepository;
use App\Vendor\Request\QuestionRequest;

class QuestionService
{
    public function __construct(
        private QuestionnaireRepository $questionnaireRepository,
        private QuestionRepository $questionRepository
    ) {
    }

    public function create(Questionnaire $questionnaire, QuestionRequest $questionRequest): Question
    {
        $question = new Question();

        $this->mapFromRequest($question, $questionRequest);

        $questionnaire->addQuestion($question);

        $this->questionnaireRepository->save($questionnaire);

        return $question;
    }

    public function update(Question $question, QuestionRequest $questionRequest): void
    {
        $this->mapFromRequest($question, $questionRequest);

        $this->questionRepository->save($question);
    }

    public function delete(Question $question): void
    {
        $this->questionRepository->delete($question);
    }

    private function mapFromRequest(Question $question, QuestionRequest $questionRequest): void
    {
        $question->setQuestion($questionRequest->question);
        $question->setOrder($questionRequest->order);
    }
}
