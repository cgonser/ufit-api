<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Question;
use App\Vendor\Entity\Questionnaire;
use App\Vendor\Repository\QuestionnaireRepository;
use App\Vendor\Repository\QuestionRepository;
use App\Vendor\Request\QuestionRequest;

class QuestionService
{
    private QuestionnaireRepository $questionnaireRepository;

    private QuestionRepository $questionRepository;

    public function __construct(
        QuestionnaireRepository $questionnaireRepository,
        QuestionRepository $questionRepository
    ) {
        $this->questionnaireRepository = $questionnaireRepository;
        $this->questionRepository = $questionRepository;
    }

    public function create(Questionnaire $questionnaire, QuestionRequest $questionRequest): Question
    {
        $question = new Question();

        $this->mapFromRequest($question, $questionRequest);

        $questionnaire->addQuestion($question);

        $this->questionnaireRepository->save($questionnaire);

        return $question;
    }

    public function update(Question $question, QuestionRequest $questionRequest)
    {
        $this->mapFromRequest($question, $questionRequest);

        $this->questionRepository->save($question);
    }

    private function mapFromRequest(Question $question, QuestionRequest $questionRequest)
    {
        $question->setQuestion($questionRequest->question);
        $question->setOrder($questionRequest->order);
    }

    public function delete(Question $question)
    {
        $this->questionRepository->delete($question);
    }
}
