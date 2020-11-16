<?php

namespace App\Vendor\Provider;

use App\Vendor\Entity\Question;
use App\Vendor\Entity\Questionnaire;
use App\Vendor\Exception\QuestionNotFoundException;
use App\Vendor\Repository\QuestionRepository;
use Ramsey\Uuid\UuidInterface;

class QuestionProvider
{
    private QuestionRepository $questionRepository;

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    public function get(UuidInterface $questionId): Question
    {
        /** @var Question|null $question */
        $question = $this->questionRepository->find($questionId);

        if (!$question) {
            throw new QuestionNotFoundException();
        }

        return $question;
    }

    public function getByQuestionnaireAndId(Questionnaire $questionnaire, UuidInterface $questionId): Question
    {
        /** @var Question|null $vendorPlan */
        $question = $this->questionRepository->findOneBy([
            'id' => $questionId,
            'questionnaire' => $questionnaire,
        ]);

        if (!$question) {
            throw new QuestionNotFoundException();
        }

        return $question;
    }

    public function findByQuestionnaire(Questionnaire $questionnaire): array
    {
        return $this->questionRepository->findBy(['questionnaire' => $questionnaire]);
    }
}
