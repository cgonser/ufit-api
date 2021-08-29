<?php

declare(strict_types=1);

namespace App\Vendor\Provider;

use App\Vendor\Entity\Question;
use App\Vendor\Entity\Questionnaire;
use App\Vendor\Exception\QuestionNotFoundException;
use App\Vendor\Repository\QuestionRepository;
use Ramsey\Uuid\UuidInterface;

class QuestionProvider
{
    public function __construct(
        private QuestionRepository $questionRepository
    ) {
    }

    public function get(UuidInterface $questionId): Question
    {
        /** @var Question|null $question */
        $question = $this->questionRepository->find($questionId);

        if (null === $question) {
            throw new QuestionNotFoundException();
        }

        return $question;
    }

    public function findOneByQuestionnaireAndId(Questionnaire $questionnaire, UuidInterface $questionId): ?object
    {
        return $this->questionRepository->findOneBy([
            'id' => $questionId,
            'questionnaire' => $questionnaire,
        ]);
    }

    public function getByQuestionnaireAndId(Questionnaire $questionnaire, UuidInterface $questionId): Question
    {
        $question = $this->findOneByQuestionnaireAndId($questionnaire, $questionId);

        if (null === $question) {
            throw new QuestionNotFoundException();
        }

        return $question;
    }

    /**
     * @return mixed[]
     */
    public function findByQuestionnaire(Questionnaire $questionnaire): array
    {
        return $this->questionRepository->findBy([
            'questionnaire' => $questionnaire,
        ]);
    }
}
