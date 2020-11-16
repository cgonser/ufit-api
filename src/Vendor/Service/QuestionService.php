<?php

namespace App\Vendor\Service;

use App\Vendor\Entity\Question;
use App\Vendor\Entity\Questionnaire;
use App\Vendor\Repository\QuestionnaireRepository;
use App\Vendor\Repository\QuestionRepository;
use App\Vendor\Request\QuestionCreateRequest;
use App\Vendor\Request\QuestionUpdateRequest;

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

    public function create(Questionnaire $questionnaire, QuestionCreateRequest $questionCreateRequest): Question
    {
        $question = new Question();
        $question->setQuestion($questionCreateRequest->question);
        $question->setOrder($questionCreateRequest->order);

        $questionnaire->addQuestion($question);

        $this->questionnaireRepository->save($questionnaire);

        return $question;
    }

    public function update(Question $question, QuestionUpdateRequest $questionUpdateRequest)
    {
        $question->setQuestion($questionUpdateRequest->question);
        $question->setOrder($questionUpdateRequest->order);

        $this->questionRepository->save($question);
    }

    public function delete(Question $question)
    {
        $this->questionRepository->delete($question);
    }
}
